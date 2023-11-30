<?php

namespace App\Repositories\Children;

use Closure;
use App\Models\Users;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\Children\Requests;
use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;

class RequestsRepository extends Repository
{
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

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('r.id', $id)->item();
    }

    public function getActiveRequest(int $childId): Item
    {
        return $this->builder()->where('r.children_id', $childId)->item();
    }

    public function save(Request $request): Requests
    {
        $model = new Requests($request->all());

        $model->setAttribute(
            'persons_requests_id',
            $this->getPersonRequestsId($request->input('children_id'))
        );

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): Requests
    {
        /** @var Requests $model */
        $model = Requests::find($id);

        if (!$model) {
            throw new NotFoundException('El recurso no ha sido encontrado');
        }

        $model->fill($request->except(['children_id', 'status', 'periods_id']));

        $model->validate()->update();

        return $model;
    }

    public function delete(int $id): bool
    {
        /** @var Requests $model */
        $model = Requests::find($id);

        if (!$model) {
            throw new NotFoundException('El recurso hijo, no ha sido encontrado');
        }

        DB::beginTransaction();

        if ($result = $model->delete()) {
            $model->documents()->delete();
        }

        DB::commit();

        return $result;
    }

    private function getPersonRequestsId(int $childrenId): ?int
    {
        $result = DB::selectOne(
            'SELECT fn_get_active_request_parent(?) as id',
            [$childrenId]
        );

        return is_null($result->id) ? null : (int) $result->id;
    }

    public function builder(): Builder
    {
        return Builder::table('children_requests', 'r')
            ->schema($this->schema())
            ->join('children as ch', 'ch.id', 'r.children_id')
            ->join('persons as pa', 'pa.id', 'ch.persons_id')
            ->join('periods as p', 'p.id', 'r.periods_id')
            ->join('education_levels as el', 'el.id', 'r.education_levels_id')
            ->leftJoin('plants as ct', 'ct.id', 'r.plants_id')
            ->leftJoin('districts as di', 'di.id', 'r.districts_id')
            ->leftJoin('provinces as pr', 'pr.id', 'di.provinces_id')
            ->leftJoin('departments as dp', 'dp.id', 'pr.departments_id')
            ->setFilters($this->filters())
            ->setCasts($this->casts())
            ->whereNull('r.deleted_at')
            ->whereNull('ch.deleted_at')
            ->whereNull('pa.deleted_at');
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

            if ($this->status) {
                $builder->whereIn('r.status', $this->status);
            }

            return $builder;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'status',
            'get_loan',
            'get_pack',
            'delivery_type',
            'address',
            'address_reference',
            'responsable' => [
                'name' => 'r.responsable_name',
                'dni' => 'r.responsable_dni',
                'phone' => 'r.responsable_phone',
            ],
            'child:ch' => [
                'id',
                'name',
                'paternal_surname',
                'maternal_surname',
                'gender',
            ],
            'parent:pa' => ['id', 'names', 'gender'],
            'period:p' => ['id', 'name', 'max_amount_loan'],
            'education_level:el' => ['id', 'name', 'amount'],
            'district:di' => ['id', 'name'],
            'province:pr' => ['id', 'name'],
            'department:dp' => ['id', 'name'],
            'plant:ct' => ['id', 'name'],
        ];
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
     * Establece el filtro por el estado de las solicitudes.
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
}
