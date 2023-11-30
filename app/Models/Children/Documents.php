<?php

namespace App\Models\Children;

use Joalvm\Utils\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documents extends Model
{
    use SoftDeletes;

    public const TYPE_DNI = 'dni';
    public const TYPE_STUDIES = 'studies';

    public const ALLOWED_TYPES = [
        self::TYPE_DNI,
        self::TYPE_STUDIES,
    ];

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = 'children_documents';
    public $timestamps = true;
    protected $fillable = [
        'children_requests_id',
        'file',
        'type',
        'status',
        'observation',
        'approved_at',
        'approved_by',
        'actions',
    ];

    protected $attributes = [
        'status' => Requests::DEFAULT_STATUS,
    ];

    protected function rules()
    {
        return [
            'children_requests_id' => [
                'required',
                'integer',
                Rule::exists('children_requests', 'id')->whereNull('deleted_at'),
            ],
            'file' => ['required', 'string'],
            'type' => [
                'required',
                'string',
                Rule::in(self::ALLOWED_TYPES),
            ],
            'status' => [
                'required',
                'string',
                Rule::in(Requests::ALLOWED_STATUS),
            ],
            'observation' => ['nullable', 'string'],
        ];
    }

    public function setActionsAttribute($value)
    {
        $actions = $this->attributes['actions'] ?? [];

        if (is_string($actions)) {
            $actions = array_map(
                function ($action) {
                    return "'${action}'::jsonb";
                },
                pgarray_to_array($actions)
            );
        }

        $actions[] = "'" . json_encode($value) . "'::jsonb";

        $this->attributes['actions'] = DB::raw('ARRAY[' . implode(',', $actions) . ']');
    }

    public function setObservationAttribute($value)
    {
        $this->attributes['observation'] = mb_strtoupper($value, 'utf-8');
    }

    public function requests(): BelongsTo
    {
        return $this->belongsTo(Requests::class, 'children_requests_id', 'id');
    }
}
