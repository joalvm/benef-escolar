<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boats extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'boats';
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
