<?php

namespace App\Repositories;

use App\Models\Plants;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Exceptions\NotFoundException;

class PlantsRepository extends Repository
{
    private $active;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('c.id', $id)->item();
    }

    public function save(Request $request): Plants
    {
        $model = new Plants($request->all());

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): Plants
    {
        $model = self::getModel($id);

        $model->fill($request->all())->validate()->save();

        return $model;
    }

    public function delete(int $id): bool
    {
        return self::getModel($id)->delete();
    }

    public static function getModel(int $id): Plants
    {
        $model = Plants::find($id);

        if (!$model) {
            throw new NotFoundException('Recurso no encontrado');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('plants', 'c')
            ->leftJoin('districts as d', 'd.id', 'c.districts_id')
            ->schema($this->schema())
            ->whereNull('c.deleted_at');
    }

    private function schema(): array
    {
        return [
            'id',
            'name',
            'district:d' => [
                'id',
                'name'
            ],
            'created_at',
        ];
    }
}
