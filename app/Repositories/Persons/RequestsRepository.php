<?php

namespace App\Repositories\Persons;

use Closure;
use App\Models\Users;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\Persons\Requests;
use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;
use App\Models\Children\Requests as ChildrenRequests;

class RequestsRepository extends Repository
{
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
     * @var int[]|null
     */
    private $units;

    /**
     * @var int[]|null
     */
    private $boats;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('r.id', $id)->item();
    }

    public function save(Request $request): Requests
    {
        $model = new Requests($request->all());

        $model->validate()->save();

        if ($request->has('person')) {
            $model->persons()->first()->fill($request->input('person'))->update();
        }

        /** @var ChildrenRequests $children */
        $children = ChildrenRequests::whereHas(
            'children',
            function ($builder) use ($model) {
                $builder->where('persons_id', $model->persons_id);
            }
        );

        foreach ($children->get() as $child) {
            $child->fill(['persons_requests_id' => $model->id])->update();
        }

        return $model;
    }

    public function update(int $id, Request $request): Requests
    {
        $model = self::getModel($id);

        $model->fill($request->except('persons_id', 'periods_id', 'person'))->validate();

        $model->update();

        return $model;
    }

    public function delete(int $id): bool
    {
        $model = self::getModel($id);

        DB::beginTransaction();

        if ($result = $model->delete()) {
            $model->documents()->delete();
            $model->childrenRequests()
            ->get()
            ->each(function (ChildrenRequests $childModel) {
                $childModel->documents()->delete();
                $childModel->delete();
            });
        }

        DB::commit();

        return $result;
    }

    public static function getModel(int $id): Requests
    {
        /** @var Requests $model */
        $model = Requests::find($id);

        if (!$model) {
            throw new NotFoundException('La solicitud no ha sido encontrado');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('persons_requests', 'r')
            ->schema($this->schema())
            ->join('persons as pa', 'pa.id', 'r.persons_id')
            ->join('periods as p', 'p.id', 'r.periods_id')
            ->leftJoin('units as u', 'u.id', 'pa.units_id')
            ->leftJoin('boats as b', 'b.id', 'pa.boats_id')
            ->setFilters($this->filters())
            ->setCasts($this->casts())
            ->where('r.periods_id', $this->periodId)
            ->whereNull('r.deleted_at')
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
                $builder->where('pa.id', $this->personId);
            }

            if (Users::ROLE_ADMIN === $this->role) {
                $builder->where('pa.responsable', '&&', array_to_pgarray([$this->personId]));
            }

            if ($this->persons) {
                $builder->whereIn('pa.id', $this->persons);
            }

            if ($this->responsable) {
                $builder->whereIn('pa.responsable', $this->responsable);
            }

            if ($this->status) {
                $builder->whereIn('r.status', $this->status);
            }

            if ($this->boats) {
                $builder->whereIn('b.id', $this->boats);
            }

            if ($this->units) {
                $builder->whereIn('u.id', $this->units);
            }

            return $builder;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'status',
            'person:pa' => [
                'id',
                'names',
                'dni',
                'gender',
                'phone',
                'unit:u' => ['id', 'name'],
                'boat:b' => ['id', 'name'],
            ],
            'period:p' => [
                'id',
                'name',
                'max_amount_loan',
            ],
            'created_at',
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

    public function setUnits($units): self
    {
        $this->units = to_array_int($units);

        return $this;
    }

    public function setBoats($boats): self
    {
        $this->boats = to_array_int($boats);

        return $this;
    }
}
