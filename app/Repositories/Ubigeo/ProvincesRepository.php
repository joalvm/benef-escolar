<?php

namespace App\Repositories\Ubigeo;

use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use Closure;

class ProvincesRepository extends Repository
{
    /**
     * @var int[]|null
     */
    private $departments;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('p.id', $id)->item();
    }

    public function builder(): Builder
    {
        return Builder::table('provinces', 'p')
            ->schema($this->schema())
            ->join('departments as d', 'd.id', 'p.departments_id')
            ->whereNull('d.deleted_at')
            ->whereNull('p.deleted_at')
            ->setFilters($this->filters());
    }

    public function filters(): Closure
    {
        return function (Builder $builder) {
            if ($this->departments) {
                $builder->whereIn('d.id', $this->departments);
            }

            return $builder;
        };
    }

    public function schema(): array
    {
        return [
            'id',
            'name',
            'department:d' => [
                'id',
                'name'
            ],
            'created_at',
        ];
    }

    /**
     * Establece el filtro por departamentos
     *
     * @param int[]|null $departments
     * @return self
     */
    public function setDepartments($departments): self
    {
        $this->departments = to_array_int($departments);

        return $this;
    }
}
