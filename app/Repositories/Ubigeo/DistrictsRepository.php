<?php

namespace App\Repositories\Ubigeo;

use Closure;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Joalvm\Utils\Collection;
use App\Components\Repository;

class DistrictsRepository extends Repository
{
    /**
     * @var int[]|null
     */
    private $departments;

    /**
     * @var int[]|null
     */
    private $provinces;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('di.id', $id)->item();
    }

    public function builder(): Builder
    {
        return Builder::table('districts', 'di')
            ->schema($this->schema())
            ->join('provinces as p', 'p.id', 'di.provinces_id')
            ->join('departments as d', 'd.id', 'p.departments_id')
            ->whereNull('d.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereNull('di.deleted_at')
            ->setFilters($this->filters());
    }

    public function filters(): Closure
    {
        return function (Builder $builder) {
            if ($this->departments) {
                $builder->whereIn('d.id', $this->departments);
            }

            if ($this->provinces) {
                $builder->whereIn('p.id', $this->provinces);
            }

            return $builder;
        };
    }

    public function schema(): array
    {
        return [
            'id',
            'name',
            'province:p' => [
                'id',
                'name'
            ],
            'department:d' => [
                'id',
                'name',
            ],
            'created_at',
        ];
    }

    /**
     * Establece el filtro por departamentos.
     *
     * @param int[]|null $departments
     */
    public function setDepartments($departments): self
    {
        $this->departments = to_array_int($departments);

        return $this;
    }

    /**
     * Establece el filtro por provincia.
     *
     * @param int[]|null $provinces
     */
    public function setProvinces($provinces): self
    {
        $this->provinces = to_array_int($provinces);

        return $this;
    }
}
