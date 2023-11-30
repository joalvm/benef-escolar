<?php

namespace App\Repositories\Custom;

use Closure;
use App\Models\Users;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\Persons\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\Children\Requests as ChildrenRequests;

class PersonsRepository extends Repository
{
    /**
     * @var int[]|null
     */
    private $units;

    /**
     * @var int[]|null
     */
    private $boats;

    /**
     * @var string[]|null
     */
    private $status;

    public function getActiveRequest(): ?array
    {
        $data = [
            'id' => '',
            'status' => '',
            'documents' => [],
        ];

        $builder = Builder::table('persons_requests', 'pr')
            ->schema($this->schemaActiveRequest())
            ->join('persons as p', 'p.id', 'pr.persons_id')
            ->join('persons_documents as pd', 'pd.persons_requests_id', 'pr.id')
            ->where([
                'pr.deleted_at' => null,
                'pd.deleted_at' => null,
                'pr.periods_id' => $this->periodId,
                'pr.persons_id' => $this->personId,
                'p.deleted_at' => null,
            ])->disablePagination();

        $items = $builder->collection();

        if (Users::ROLE_ADMIN == $this->role) {
            $builder->where('p.responsable', '&&', array_to_pgarray([$this->personId]));
        }

        if ($items->isEmpty()) {
            return null;
        }

        foreach ($items->all() as $item) {
            $data['id'] = $item['id'];
            $data['status'] = $item['status'];
            $data['documents'][] = $item['document'];
        }

        return $data;
    }

    public function getFullRequests(int $requestId): Item
    {
        $builder = Builder::table('persons_requests', 'pr')
            ->schema($this->schemaFullRequests())
            ->join('persons as p', 'p.id', 'pr.persons_id')
            ->join('units as u', 'u.id', 'p.units_id')
            ->leftJoin('boats as b', 'b.id', 'p.boats_id')
            ->where([
                'pr.deleted_at' => null,
                'p.deleted_at' => null,
                'pr.id' => $requestId,
                'pr.periods_id' => $this->periodId,
            ])->setCasts(function ($item) {
                $item['documents'] = json_decode($item['documents'], true);

                return $item;
            });

        if (Users::ROLE_ADMIN == $this->role) {
            $builder->where('p.responsable', '&&', array_to_pgarray([$this->personId]));
        }

        return $builder->disablePagination()->item();
    }

    public function getCountsRequests(): Item
    {
        $builder = Builder::table('persons_requests', 'pr')
            ->select([
                DB::raw("count(pr.id) FILTER (WHERE pr.status = 'pending') AS pendings"),
                DB::raw("count(pr.id) FILTER (WHERE pr.status = 'observed') AS observeds"),
                DB::raw("count(pr.id) FILTER (WHERE pr.status = 'approved') AS approveds"),
            ])->join('persons as p', 'p.id', 'pr.persons_id')
            ->where([
                'p.deleted_at' => null,
                'pr.deleted_at' => null,
                'pr.periods_id' => $this->periodId,
            ]);

        if (Users::ROLE_ADMIN == $this->role) {
            $builder->where('p.responsable', '&&', array_to_pgarray([$this->personId]));
        }

        if ($this->boats) {
            $builder->whereIn('p.boats_id', $this->boats);
        }

        if ($this->units) {
            $builder->whereIn('p.units_id', $this->units);
        }

        if ($this->status) {
            $builder->whereIn('pr.status', $this->status);
        }

        return $builder->item();
    }

    public function getDataFilesZip(): Collection
    {
        $builder = Builder::table('persons_requests', 'pr')
            ->select([
                'p.id',
                'p.names',
                'u.id AS unit_id',
                'u.name AS unit',
                'b.id AS boat_id',
                'b.name AS boat',
            ])
            ->selectSub($this->sqPersonDocuments(), 'documents')
            ->selectSub($this->sqChildrenDocuments(), 'children')
            ->join('persons as p', 'p.id', 'pr.persons_id')
            ->join('units as u', 'u.id', 'p.units_id')
            ->leftJoin('boats as b', 'b.id', 'p.boats_id')
            ->where([
                'pr.deleted_at' => null,
                'p.deleted_at' => null,
                'pr.periods_id' => $this->periodId,
                'pr.status' => Requests::STATUS_APPROVED,
            ]);

        if (Users::ROLE_ADMIN == $this->role) {
            $builder->where(
                'p.responsable',
                '&&',
                array_to_pgarray([$this->personId])
            );
        }

        if ($this->boats) {
            $builder->whereIn('p.boats_id', $this->boats);
        }

        if ($this->units) {
            $builder->whereIn('p.units_id', $this->units);
        }

        return $builder->disablePagination()->collection()->each(function (&$item) {
            $item->documents = json_decode($item->documents, true);
            $item->children = json_decode($item->children, true);
        });
    }

    private function schemaActiveRequest()
    {
        return [
            'id',
            'status',
            'document:pd' => [
                'id',
                'file' => DB::raw('file'),
                'status',
                'observation',
            ],
        ];
    }

    private function schemaFullRequests(): array
    {
        return [
            'id',
            'status',
            'person:p' => [
                'id',
                'names',
                'paternal_surname',
                'maternal_surname',
                'dni',
                'phone',
                'unit:u' => ['id', 'name'],
                'boat:b' => ['id', 'name'],
            ],
            'documents' => $this->sqPersonDocuments(),
        ];
    }

    private function sqChildrenDocuments(): Closure
    {
        return function (Builder $builder) {
            $builder->selectRaw('jsonb_agg(row_to_json(sq))')
                ->fromSub(function (Builder $query) {
                    $query->select(
                        'cd.id',
                        'cd.file',
                        'cd.type',
                        'cd.status',
                        'cd.observation',
                        'cd.updated_at as last_update',
                        DB::raw('c.name || \' \' || c.paternal_surname || \' \' || c.maternal_surname as fullname')
                    )->from('children_documents', 'cd')
                    ->join('children_requests as cr', 'cr.id', 'cd.children_requests_id')
                    ->join('children as c', 'c.id', 'cr.children_id')
                    ->where([
                        'cd.deleted_at' => null,
                        'cr.deleted_at' => null,
                        'c.deleted_at' => null,
                        'cr.status' => ChildrenRequests::STATUS_APPROVED,
                        'c.persons_id' => DB::raw('p.id'),
                    ])
                    ->orderBy('cd.id', 'asc');

                    if ($this->periodId) {
                        $query->where('cr.periods_id', $this->periodId);
                    }
                }, 'sq');
        };
    }

    private function sqPersonDocuments(): Closure
    {
        return function (Builder $query) {
            $query->selectRaw('jsonb_agg(row_to_json(sq))')
                ->fromSub(function (Builder $query) {
                    $query->select(
                        'pd.id',
                        'pd.file',
                        'pd.status',
                        'pd.observation',
                        'pd.updated_at as last_update'
                    )->from('persons_documents', 'pd')
                    ->where('pd.persons_requests_id', DB::raw('pr.id'))
                    ->whereNull('pd.deleted_at')
                    ->orderBy('pd.id', 'asc');
                }, 'sq');
        };
    }

    /**
     * Establece el filtro por unidad.
     *
     * @param int[]|null $units
     */
    public function setUnits($units): self
    {
        $this->units = to_array_int($units);

        return $this;
    }

    /**
     * Establece el filtro por embarcación.
     *
     * @param int[]|null $boats
     */
    public function setBoats($boats): self
    {
        $this->boats = to_array_int($boats);

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
}
