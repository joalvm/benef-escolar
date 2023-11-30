<?php

namespace App\Repositories\Children;

use Closure;
use App\Models\Users;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\Children\Children;
use App\Exceptions\NotFoundException;

class ChildrenRepository extends Repository
{
    /**
     * @var string|null
     */
    private $gender;

    /**
     * @var int|null
     */
    private $parent;

    public function all(): Collection
    {
        return $this->builder()->collection();
    }

    public function find(int $id): Item
    {
        return $this->builder()->where('c.id', $id)->item();
    }

    public function save(Request $request): Children
    {
        $model = new Children($request->all());

        if (Users::ROLE_USER === $this->role) {
            $model->setAttribute('persons_id', $this->personId);
        }

        $model->validate()->save();

        return $model;
    }

    public function update(int $id, Request $request): Children
    {
        $model = self::getModel($id);

        $model->fill($request->except('parent_id'))->validate()->update();

        return $model;
    }

    public static function getModel(int $id): Children
    {
        $model = Children::find($id);

        if (!$model) {
            throw new NotFoundException(trans('resource.not_found'));
        }

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('children', 'c')
            ->schema($this->schema())
            ->join('public.persons as p', 'p.id', 'c.persons_id')
            ->where([
                'p.deleted_at' => null,
                'c.deleted_at' => null,
            ])->setFilters($this->filters());
    }

    private function filters(): Closure
    {
        return function (Builder $builder) {
            if (Users::ROLE_USER === $this->role) {
                $builder->where('p.id', $this->personId);
            }

            if ($this->parent) {
                $builder->where('p.id', $this->parent);
            }

            if ($this->gender) {
                $builder->where('c.gender', $this->gender);
            }
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'name',
            'paternal_surname',
            'maternal_surname',
            'gender',
            'birth_date',
            'parent:p' => [
                'id',
                'names',
                'gender',
                'dni',
            ],
            'created_at',
        ];
    }

    public function setParent($parent): self
    {
        $this->parent = to_int($parent);

        return $this;
    }

    public function setGender($gender): self
    {
        $this->gender = in_array($gender, Children::GENDERS) ? $gender : null;

        return $this;
    }
}
