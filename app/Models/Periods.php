<?php

namespace App\Models;

use Joalvm\Utils\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periods extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'periods';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'start_date',
        'finish_date',
        'amount_bonds',
        'max_amount_loan',
        'max_children',
        'active',
    ];

    protected $attributes = [
        'active' => false,
    ];

    protected function rules()
    {
        return [
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'finish_date' => ['required', 'date', 'after_or_equal:start_date'],
            'amount_bonds' => ['required', 'numeric'],
            'max_amount_loan' => ['required', 'numeric'],
            'max_children' => ['required', 'numeric'],
            'active' => ['required', 'boolean'],
        ];
    }
}
