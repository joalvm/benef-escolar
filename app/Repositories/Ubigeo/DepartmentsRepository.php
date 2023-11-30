<?php

namespace App\Repositories\Ubigeo;

use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Joalvm\Utils\Collection;
use App\Components\Repository;

class DepartmentsRepository extends Repository
{
    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('d.id', $id)->item();
    }

    public function builder(): Builder
    {
        return Builder::table('departments', 'd')
            ->schema($this->schema())
            ->whereNull('d.deleted_at');
    }

    public function schema(): array
    {
        return [
            'id',
            'name',
            'created_at',
        ];
    }
}
