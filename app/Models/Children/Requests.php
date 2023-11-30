<?php

namespace App\Models\Children;

use Closure;
use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Persons\Requests as PersonsRequests;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requests extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_OBSERVED = 'observed';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CLOSED = 'closed';

    public const DELIVERY_TYPE_PICK_IN_PLANT = 'pick_in_plant';
    public const DELIVERY_TYPE_DELIVERY = 'delivery';

    public const DEFAULT_STATUS = self::STATUS_PENDING;
    public const DEFAULT_DELIVERY_TYPE = self::DELIVERY_TYPE_DELIVERY;

    public const ALLOWED_STATUS = [
        self::STATUS_PENDING,
        self::STATUS_OBSERVED,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_CLOSED,
    ];

    public const ALLOWED_DELIVERY_TYPES = [
        self::DELIVERY_TYPE_PICK_IN_PLANT,
        self::DELIVERY_TYPE_DELIVERY,
    ];

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'children_requests';
    public $timestamps = true;
    protected $fillable = [
        'children_id',
        'periods_id',
        'persons_requests_id',
        'education_levels_id',
        'get_loan',
        'get_pack',
        'delivery_type',
        'plants_id',
        'responsable_name',
        'responsable_dni',
        'responsable_phone',
        'districts_id',
        'address',
        'address_reference',
        'status',
    ];

    protected $attributes = [
        'delivery_type' => self::DEFAULT_DELIVERY_TYPE,
        'status' => self::DEFAULT_STATUS,
        'get_loan' => true,
        'get_pack' => true,
    ];

    protected function rules()
    {
        return [
            'children_id' => [
                'required',
                'integer',
                Rule::exists('children', 'id')->whereNull('deleted_at'),
            ],
            'persons_requests_id' => [
                'nullable',
                'integer',
                Rule::exists('persons_requests', 'id')->whereNull('deleted_at'),
            ],
            'periods_id' => [
                'required',
                'integer',
                Rule::exists('periods', 'id')->whereNull('deleted_at'),
            ],
            'education_levels_id' => [
                'required',
                'integer',
                Rule::exists('education_levels', 'id')->whereNull('deleted_at'),
            ],
            'get_loan' => ['required', 'boolean'],
            'get_pack' => ['required', 'boolean'],
            'delivery_type' => [
                'nullable',
                'string',
                Rule::in(self::ALLOWED_DELIVERY_TYPES),
            ],
            'plants_id' => [
                'nullable',
                'integer',
                Rule::exists('plants', 'id')->whereNull('deleted_at'),
            ],
            'responsable_name' => ['nullable', 'string'],
            'responsable_dni' => ['nullable', 'string', 'size:8'],
            'responsable_phone' => ['nullable', 'string'],
            'districts_id' => [
                'nullable',
                'integer',
                Rule::exists('districts', 'id')->whereNull('deleted_at'),
            ],
            'address' => ['nullable', 'string'],
            'address_reference' => ['nullable', 'string'],
            'status' => [
                'required',
                'string',
                Rule::in(self::ALLOWED_STATUS),
            ],
        ];
    }

    public function setResponsableNameAttribute($value)
    {
        $this->attributes['responsable_name'] = mb_strtoupper($value, 'utf-8');
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = mb_strtoupper($value, 'utf-8');
    }

    public function setAddressReferenceAttribute($value)
    {
        $this->attributes['address_reference'] = mb_strtoupper($value, 'utf-8');
    }

    public function ruleUniquechildrenPerPeriod(): Closure
    {
        return function ($attr, $val, $fail) {
            $query = DB::table('children_requests', 'r')
                ->where([
                    'r.children_id' => $this->attributes['children_id'],
                    'r.periods_id' => $val,
                    'r.deleted_at' => null,
                ]);

            if ($query->exists()) {
                $fail(trans('validation.unique', [
                    'attribute' => 'children, periods',
                ]));
            }
        };
    }

    public function children(): BelongsTo
    {
        return $this->belongsTo(Children::class, 'children_id', 'id');
    }

    public function personsRequests(): BelongsTo
    {
        return $this->belongsTo(PersonsRequests::class, 'persons_requests_id', 'id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Documents::class, 'children_requests_id', 'id');
    }
}
