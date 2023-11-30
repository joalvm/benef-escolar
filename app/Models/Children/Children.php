<?php

namespace App\Models\Children;

use App\Models\Persons;
use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Children extends Model
{
    use SoftDeletes;

    public const GENDERS = ['masculino', 'femenino'];

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'children';
    public $timestamps = true;
    protected $fillable = [
        'persons_id',
        'name',
        'paternal_surname',
        'maternal_surname',
        'gender',
        'birth_date',
    ];

    protected function rules()
    {
        return [
            'persons_id' => [
                'required',
                'integer',
                Rule::exists('persons', 'id')->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string'],
            'paternal_surname' => ['required', 'string'],
            'maternal_surname' => ['required', 'string'],
            'gender' => ['required', 'string', Rule::in(self::GENDERS)],
            'birth_date' => ['required', 'date'],
        ];
    }

    public function setNameAttribute($val)
    {
        $this->attributes['name'] = mb_strtoupper($val, 'utf-8');
    }

    public function setPaternalSurnameAttribute($val)
    {
        $this->attributes['paternal_surname'] = mb_strtoupper($val, 'utf-8');
    }

    public function setMaternalSurnameAttribute($val)
    {
        $this->attributes['maternal_surname'] = mb_strtoupper($val, 'utf-8');
    }

    public function persons(): BelongsTo
    {
        return $this->belongsTo(Persons::class, 'persons_id', 'id');
    }

    public function requests(): HasOne
    {
        return $this->hasOne(Requests::class, 'children', 'id');
    }
}
