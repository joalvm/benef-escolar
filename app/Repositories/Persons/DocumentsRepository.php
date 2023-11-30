<?php

namespace App\Repositories\Persons;

use Closure;
use DateTime;
use DateTimeZone;
use App\Models\Users;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\Persons\Requests;
use App\Models\Persons\Documents;
use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;

class DocumentsRepository extends Repository
{
    /**
     * @var int[]|null
     */
    private $requests;
    /**
     * @var int[]|null
     */
    private $persons;

    /**
     * @var int[]|null
     */
    private $responsable;

    /**
     * @var string[]|null
     */
    private $status;

    /**
     * @var string[]|null
     */
    private $requestStatus;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('r.id', $id)->item();
    }

    public function save(Request $request): Documents
    {
        $model = new Documents($request->all());

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): Documents
    {
        $now = new DateTime('now', new DateTimeZone('America/Lima'));
        $model = self::getModel($id);
        $currentStatus = $model->getAttribute('status');

        $model->fill($request->except(['periods_id', 'requests_id']))->validate();

        if ($request->has('status') and Requests::STATUS_APPROVED !== $currentStatus) {
            if (Requests::STATUS_APPROVED === $request->input('status')) {
                $model->setAttribute('approved_at', $now->format(DateTime::ISO8601));
                $model->setAttribute('approved_by', $this->personId);
            }
        }

        $model->update();

        return $model;
    }

    public static function getModel(int $id): Documents
    {
        $model = Documents::find($id);

        if (!$model) {
            throw new NotFoundException('El recurso documento no existe');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('persons_documents', 'rd')
            ->schema($this->schema())
            ->join('persons_requests as r', 'r.id', 'rd.persons_requests_id')
            ->join('persons as pa', 'pa.id', 'r.persons_id')
            ->join('periods as p', 'p.id', 'r.periods_id')
            ->setFilters($this->filters())
            ->setCasts($this->casts())
            ->whereNull('r.deleted_at')
            ->whereNull('rd.deleted_at')
            ->whereNull('pa.deleted_at');
    }

    private function casts(): Closure
    {
        return function ($item) {
            cast_float($item, ['period.max_amount_loan']);

            return $item;
        };
    }

    private function filters(): Closure
    {
        return function (Builder $builder) {
            if (Users::ROLE_USER === $this->role) {
                $builder->where([
                    'pa.id' => $this->personId,
                    'r.periods_id' => DB::raw('fn_get_active_period()'),
                ]);
            }

            if ($this->requests) {
                $builder->whereIn('r.id', $this->requests);
            }

            if ($this->persons) {
                $builder->whereIn('pa.id', $this->persons);
            }

            if ($this->responsable) {
                $builder->whereIn('pa.responsable', $this->responsable);
            }

            if ($this->requestStatus) {
                $builder->whereIn('r.status', $this->requestStatus);
            }

            if ($this->status) {
                $builder->whereIn('rd.status', $this->status);
            }

            return $builder;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'file' => DB::raw('file'),
            'status',
            'observation',
            'request:r' => [
                'id',
                'status',
            ],
            'persons:pa' => [
                'id',
                'names',
                'gender',
            ],
            'period:p' => [
                'id',
                'max_amount_loan',
            ],
        ];
    }

    /**
     * Establece el filtro por solicitud.
     *
     * @param int[]|null $requests
     */
    public function setRequests($requests): self
    {
        $this->requests = to_array_int($requests);

        return $this;
    }

    /**
     * Establece el filtro para buscar por medio de padres.
     *
     * @param int[]|null $persons
     */
    public function setPersons($persons): self
    {
        $this->persons = to_array_int($persons);

        return $this;
    }

    /**
     * Establece el filtro por persona responsable del seguimiento de cada padre.
     *
     * @param int[]|null $responsable
     */
    public function setResponsable($responsable): self
    {
        $this->responsable = to_array_int($responsable);

        return $this;
    }

    /**
     * Establece el filtro por el estado del documento en la solicitud.
     *
     * @param string[]|null $status
     */
    public function setStatus($status): self
    {
        $this->status = array_filter(
            to_array_str($status),
            function (string $sta) {
                return in_array($sta, Requests::ALLOWED_STATUS);
            }
        );

        return $this;
    }

    /**
     * Establece el filtro por el estado de las solicitudes.
     *
     * @param string[]|null $status
     */
    public function setRequestStatus($requestStatus): self
    {
        $this->requestStatus = array_filter(
            to_array_str($requestStatus),
            function (string $sta) {
                return in_array($sta, Requests::ALLOWED_STATUS);
            }
        );

        return $this;
    }
}
