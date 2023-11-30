<?php

namespace App\Repositories;

use Closure;
use App\Models\Users;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\EducationLevels;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;

class EducationLevelsRepository extends Repository
{
    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('e.id', $id)->item();
    }

    public function save(Request $request): EducationLevels
    {
        $model = new EducationLevels($request->all());

        if (Users::ROLE_SUPER_ADMIN !== $this->role) {
            throw new ForbiddenException('No tiene permiso para esta acción');
        }

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): EducationLevels
    {
        if (Users::ROLE_SUPER_ADMIN !== $this->role) {
            throw new ForbiddenException('No tiene permiso para esta acción');
        }

        $model = self::getModel($id);

        $model->fill($request->all())->validate()->save();

        return $model;
    }

    public function delete(int $id): bool
    {
        if (Users::ROLE_SUPER_ADMIN !== $this->role) {
            throw new ForbiddenException('No tiene permiso para esta acción');
        }

        return self::getModel($id)->delete();
    }

    public static function getModel(int $id): EducationLevels
    {
        $model = EducationLevels::find($id);

        if (!$model) {
            throw new NotFoundException('Recurso no encontrado');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('education_levels', 'e')
            ->schema($this->schema())
            ->whereNull('e.deleted_at')
            ->setCasts($this->casts());
    }

    public function casts(): Closure
    {
        return function ($item) {
            cast_int($item, ['amount']);

            return $item;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'name',
            'amount',
            'created_at',
        ];
    }
}
