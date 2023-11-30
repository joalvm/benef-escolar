<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationLevels extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'education_levels';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'amount',
    ];

    protected function rules()
    {
        return [
            'name' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
        ];
    }
}
