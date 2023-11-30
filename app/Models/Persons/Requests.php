<?php

namespace App\Models\Persons;

use Closure;
use App\Models\Persons;
use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Children\Requests as ChildrenRequests;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requests extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_OBSERVED = 'observed';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const DEFAULT_STATUS = self::STATUS_PENDING;

    public const ALLOWED_STATUS = [
        self::STATUS_PENDING,
        self::STATUS_OBSERVED,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'persons_requests';
    public $timestamps = true;
    protected $fillable = [
        'persons_id',
        'periods_id',
        'status',
    ];

    protected $attributes = [
        'status' => self::DEFAULT_STATUS,
    ];

    protected function rules()
    {
        return [
            'persons_id' => [
                'required',
                'integer',
                Rule::exists('persons', 'id')->whereNull('deleted_at'),
            ],
            'periods_id' => [
                'required',
                'integer',
                Rule::exists('periods', 'id')->whereNull('deleted_at'),
                $this->ruleUniquePersonRequestPerPeriod(),
            ],
            'status' => [
                'required',
                'string',
                Rule::in(self::ALLOWED_STATUS),
            ],
        ];
    }

    public function ruleUniquePersonRequestPerPeriod(): Closure
    {
        return function ($attr, $val, $fail) {
            $query = DB::table('persons_requests', 'r')
                ->where([
                    'r.persons_id' => $this->attributes['persons_id'],
                    'r.periods_id' => $val,
                    'r.deleted_at' => null,
                ]);

            if ($query->exists()) {
                $fail('Solo puede iniciar un solicitud por periodo');
            }
        };
    }

    public function persons(): BelongsTo
    {
        return $this->belongsTo(Persons::class, 'persons_id', 'id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Documents::class, 'persons_requests_id', 'id');
    }

    public function childrenRequests(): HasMany
    {
        return $this->hasMany(ChildrenRequests::class, 'persons_requests_id', 'id');
    }
}
