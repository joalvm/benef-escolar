<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plants extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'plants';
    public $timestamps = true;
    protected $fillable = [
        'districts_id',
        'name',
    ];

    protected function rules()
    {
        return [
            'districts_id' => ['required', 'integer', Rule::exists('districts', 'id')->where('deleted_at')],
            'name' => ['required', 'string'],
        ];
    }
}
