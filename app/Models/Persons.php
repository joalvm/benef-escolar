<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Persons extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_VERIFIED = 'verified';

    public const GENDERS = ['masculino', 'femenino'];
    public const ALLOWED_STATUS = [
        self::STATUS_PENDING,
        self::STATUS_REGISTERED,
        self::STATUS_VERIFIED,
    ];

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'persons';
    public $timestamps = true;
    protected $fillable = [
        'units_id',
        'boats_id',
        'names',
        'dni',
        'gender',
        'birth_date',
        'hiring_date',
        'email',
        'phone',
        'status',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    protected function rules()
    {
        return [
            'units_id' => [
                'required',
                'integer',
                Rule::exists('units', 'id')->whereNull('deleted_at'),
            ],
            'boats_id' => [
                'nullable',
                'integer',
                Rule::exists('boats', 'id')->whereNull('deleted_at'),
            ],
            'names' => ['required', 'string'],
            'dni' => ['nullable', 'string', 'min:8', 'max:8'],
            'gender' => ['required', 'string', Rule::in(self::GENDERS)],
            'email' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'hiring_date' => ['required', 'date'],
            'status' => ['required', Rule::in(self::ALLOWED_STATUS)],
        ];
    }

    public function users(): HasOne
    {
        return $this->hasOne(Users::class, 'persons_id', 'id');
    }

    public function boats(): BelongsTo
    {
        return $this->belongsTo(Boats::class, 'boats_id', 'id');
    }
}
