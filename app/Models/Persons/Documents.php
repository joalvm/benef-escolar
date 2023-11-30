<?php

namespace App\Models\Persons;

use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documents extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'persons_documents';
    public $timestamps = true;
    protected $fillable = [
        'persons_requests_id',
        'file',
        'type',
        'status',
        'observation',
        'approved_by',
        'approved_at',
    ];

    protected $attributes = [
        'status' => Requests::DEFAULT_STATUS,
    ];

    protected function rules()
    {
        return [
            'persons_requests_id' => [
                'required',
                'integer',
                Rule::exists('persons_requests', 'id')->whereNull('deleted_at'),
            ],
            'file' => ['required', 'string'],
            'status' => [
                'required',
                'string',
                Rule::in(Requests::ALLOWED_STATUS),
            ],
            'observation' => ['nullable', 'string'],
        ];
    }

    public function requests(): BelongsTo
    {
        return $this->belongsTo(Requests::class, 'persons_requests_id', 'id');
    }
}
