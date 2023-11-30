<?php

namespace App\Repositories\Children;

use Closure;
use DateTime;
use DateTimeZone;
use App\Models\Users;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\Children\Requests;
use App\Models\Children\Documents;
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
    private $children;

    /**
     * @var int[]|null
     */
    private $responsable;

    /**
     * @var int[]|null
     */
    private $plants;

    /**
     * @var string[]|null
     */
    private $status;

    /**
     * @var string[]|null
     */
    private $requestStatus;

    /**
     * @var string[]|null
     */
    private $documentTypes;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('rd.id', $id)->item();
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

        $model->fill(
            $request->except([
                'children_requests_id',
                'approved_at',
                'approved_by',
            ])
        )->validate();

        if ($request->has('status') and Requests::STATUS_APPROVED !== $currentStatus) {
            $model->setAttribute('actions', [
                'by' => $this->personId,
                'action' => $request->input('status'),
                'at' => $now->format(DateTime::ISO8601),
            ]);

            if (Requests::STATUS_APPROVED === $request->input('status')) {
                $model->setAttribute('approved_at', $now->format(DateTime::ISO8601));
                $model->setAttribute('approved_by', $this->personId);
            }
        }

        $model->update();

        return $model;
    }

    public function delete(int $id): bool
    {
        return self::getModel($id)->delete();
    }

    public static function getModel(int $id): Documents
    {
        $model = Documents::find($id);

        if (!$model) {
            throw new NotFoundException('El recurso no ha sido encontrado');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('children_documents', 'rd')
            ->schema($this->schema())
            ->join('children_requests as r', 'r.id', 'rd.children_requests_id')
            ->join('children as ch', 'ch.id', 'r.children_id')
            ->join('persons as pa', 'pa.id', 'ch.persons_id')
            ->join('periods as p', 'p.id', 'r.periods_id')
            ->join('education_levels as el', 'el.id', 'r.education_levels_id')
            ->leftJoin('plants as ct', 'ct.id', 'r.plants_id')
            ->whereNull('rd.deleted_at')
            ->whereNull('r.deleted_at')
            ->whereNull('ch.deleted_at')
            ->whereNull('pa.deleted_at')
            ->setCasts($this->casts())
            ->setFilters($this->filters());
    }

    private function casts(): Closure
    {
        return function ($item) {
            cast_float($item, [
                'period.max_amount_loan',
                'education_level.amount',
            ]);

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

            if ($this->children) {
                $builder->whereIn('ch.id', $this->children);
            }

            if ($this->persons) {
                $builder->whereIn('pa.id', $this->persons);
            }

            if ($this->responsable) {
                $builder->whereIn('pa.responsable', $this->responsable);
            }

            if ($this->plants) {
                $builder->whereIn('ct.id', $this->plants);
            }

            if ($this->requestStatus) {
                $builder->whereIn('r.status', $this->requestStatus);
            }

            if ($this->status) {
                $builder->whereIn('rd.status', $this->status);
            }

            if ($this->documentTypes) {
                $builder->whereIn('rd.type', $this->documentTypes);
            }

            return $builder;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'file' => DB::raw('rd.file'),
            'type',
            'status',
            'observation',
            'request:r' => [
                'id',
                'get_loan',
                'get_pack',
                'persons_requests_id',
                'status',
            ],
            'child:ch' => [
                'id',
                'name',
                'paternal_surname',
                'maternal_surname',
                'gender',
            ],
            'parent:pa' => [
                'id',
                'names',
                'gender',
            ],
            'period:p' => [
                'id',
                'name',
                'max_amount_loan',
            ],
            'education_level:el' => [
                'id',
                'name',
                'amount',
            ],
            'plant:ct' => [
                'id',
                'name',
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
     * Establece el filtro por hijo.
     *
     * @param int[]|null $children
     */
    public function setChildren($children): self
    {
        $this->children = to_array_int($children);

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
     * Establece el filtro por ciudad de recojo de cada pack.
     *
     * @param int[]|null $plants
     */
    public function setPlants($plants): self
    {
        $this->plants = to_array_int($plants);

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

    /**
     * Establece el filtro por el tipo de documento.
     *
     * @param string[]|null $status
     */
    public function setDocumentTypes($documentTypes): self
    {
        $this->documentTypes = array_filter(
            to_array_str($documentTypes),
            function (string $doc) {
                return in_array($doc, Documents::ALLOWED_TYPES);
            }
        );

        return $this;
    }
}
