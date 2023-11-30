<?php

namespace App\Repositories;

use App\Models\Boats;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use App\Components\Repository;
use App\Exceptions\NotFoundException;

class BoatsRepository extends Repository
{
    private $active;

    public function all(): array
    {
        return $this->builder()->get()->all();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('b.id', $id)->item();
    }

    public function save(Request $request): Boats
    {
        $model = new Boats($request->all());

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): Boats
    {
        $model = self::getModel($id);

        $model->fill($request->all())->validate()->update();

        return $model;
    }

    public function delete(int $id): bool
    {
        return self::getModel($id)->delete();
    }

    public static function getModel(int $id): Boats
    {
        $model = Boats::find($id);

        if (!$model) {
            throw new NotFoundException('El recurso Embarcación no encontrado');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('boats', 'b')
            ->schema($this->schema())
            ->whereNull('b.deleted_at');
    }

    private function schema(): array
    {
        return [
            'id',
            'name',
            'created_at',
        ];
    }
}
