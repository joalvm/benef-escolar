<?php

namespace App\Repositories;

use Closure;
use App\Models\Users;
use App\Models\Persons;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Exceptions\NotFoundException;

class PersonsRepository extends Repository
{
    private $active;

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
        return $this->builder()->where('p.id', $id)->item();
    }

    public function save(Request $request): Persons
    {
        $model = new Persons($request->all());

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): Persons
    {
        $model = self::getModel($id);

        $model->fill($request->all())->validate()->save();

        return $model;
    }

    public function delete(int $id): bool
    {
        return self::getModel($id)->delete();
    }

    public static function getModel(int $id): Persons
    {
        $model = Persons::find($id);

        if (!$model) {
            throw new NotFoundException('Recurso no encontrado');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('persons', 'p')
            ->schema($this->schema())
            ->leftJoin('units as u', 'u.id', 'p.units_id')
            ->leftJoin('boats as b', 'b.id', 'p.boats_id')
            ->whereNull('p.deleted_at')
            ->setFilters($this->filters());
    }

    public function filters(): Closure
    {
        return function (Builder $builder) {
            if (Users::ROLE_USER === $this->role) {
                $builder->where('p.id', $this->personId);
            }

            if (Users::ROLE_ADMIN === $this->role) {
                $builder->where('p.responsable', '&&', array_to_pgarray([$this->personId]));
            }

            if ($this->units) {
                $builder->whereIn('p.units_id', $this->units);
            }

            if ($this->boats) {
                $builder->whereIn('p.boats_id', $this->boats);
            }

            return $builder;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'names',
            'dni',
            'email',
            'gender',
            'birth_date',
            'hiring_date',
            'phone',
            'status',
            'unit:u' => ['id', 'name'],
            'boat:b' => ['id', 'name'],
            'created_at',
        ];
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
