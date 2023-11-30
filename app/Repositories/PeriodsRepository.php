<?php

namespace App\Repositories;

use Closure;
use App\Models\Periods;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Exceptions\NotFoundException;

class PeriodsRepository extends Repository
{
    private $active;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('p.id', $id)->item();
    }

    public function getActive(): Item
    {
        return $this->builder()->where('p.active', true)->item();
    }

    public function save(Request $request): Periods
    {
        $model = new Periods($request->all());

        if (true === $request->input('active', false)) {
            $this->resetActive();
        }

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): Periods
    {
        $model = self::getModel($id);

        if (true == $request->input('active') and !$model->active) {
            $this->resetActive();
        }

        $model->fill($request->all())->validate()->update();

        return $model;
    }

    public function delete(int $id): bool
    {
        return self::getModel($id)->delete();
    }

    private function resetActive(): void
    {
        Periods::where('active', true)->update([
            'active' => false,
        ]);
    }

    public static function getModel(int $id): Periods
    {
        $model = Periods::find($id);

        if (!$model) {
            throw new NotFoundException('Recurso no encontrado');
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('periods', 'p')
            ->schema($this->schema())
            ->whereNull('p.deleted_at')
            ->setFilters($this->filters())
            ->setCasts($this->casts());
    }

    private function filters(): Closure
    {
        return function (Builder $builder) {
            if ($this->active) {
                $builder->where('p.active', $this->active);
            }

            return $builder;
        };
    }

    private function casts(): Closure
    {
        return function ($item) {
            cast_int($item, ['amount_bonds', 'max_amount_loan', 'max_children']);

            return $item;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'name',
            'start_date',
            'finish_date',
            'amount_bonds',
            'max_amount_loan',
            'active',
            'max_children',
            'created_at',
        ];
    }
}
