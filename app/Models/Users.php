<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Users extends Model
{
    use SoftDeletes;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public const ALLOWED_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];

    /**
     * Guarda la contraseña original generada al momento de la inserción.
     *
     * @var string
     */
    private $tempPassword = '';

    protected $table = 'users';
    public $timestamps = true;

    protected $fillable = [
        'persons_id',
        'role',
        'password',
        'salt',
        'recovery_token',
        'verification_token',
        'verified_at',
        'last_login',
        'enabled',
    ];

    protected $attributes = [
        'enabled' => true,
        'role' => self::ROLE_USER,
    ];

    protected function rules()
    {
        return [
            'persons_id' => [
                'required',
                Rule::exists('persons', 'id')->whereNull('deleted_at'),
            ],
            'role' => ['required', 'string', Rule::in(self::ALLOWED_ROLES)],
            'password' => ['required', 'string'],
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Sessions::class, 'users_id', 'id');
    }

    public function persons(): BelongsTo
    {
        return $this->belongsTo(Persons::class, 'persons_id', 'id');
    }

    /**
     * Get guarda la contraseña original generada al momento de la inserción.
     */
    public function getTempPassword(): string
    {
        return $this->tempPassword;
    }

    /**
     * Set guarda la contraseña original generada al momento de la inserción.
     */
    public function setTempPassword(string $tempPassword): self
    {
        $this->tempPassword = $tempPassword;

        return $this;
    }
}
