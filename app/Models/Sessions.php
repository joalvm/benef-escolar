<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sessions extends Model
{
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    protected $table = 'users_sessions';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'users_id',
        'token',
        'expire',
        'ip',
        'browser',
        'browser_version',
        'platform',
        'closed_at',
        'created_at',
    ];

    protected function rules()
    {
        return [
            'users_id' => ['required', 'integer'],
            'token' => ['required', 'string'],
            'expire' => ['required', 'integer'],
            'ip' => ['required', 'string'],
            'browser' => ['required', 'string'],
            'browser_version' => ['required', 'string'],
            'platform' => ['nullable', 'string'],
            'closed_at' => ['nullable', 'string'],
        ];
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'users_id', 'id');
    }
}
