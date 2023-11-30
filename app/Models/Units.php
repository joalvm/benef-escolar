<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Units extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'units';
    public $timestamps = true;
    protected $fillable = [
        'name',
    ];

    protected function rules()
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}
