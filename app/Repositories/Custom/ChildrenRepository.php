<?php

namespace App\Repositories\Custom;

use Closure;
use App\Models\Users;
use Joalvm\Utils\Builder;
use Joalvm\Utils\Collection;
use App\Components\Repository;
use App\Models\Children\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class ChildrenRepository extends Repository
{
    private $boats;

    private $units;

    private $status;

    public function getChildrenRequests(int $periodId): Collection
    {
        $builder = Builder::table('children', 'c')
            ->schema($this->schemaChildrenRequests())
            ->join('persons as p', 'p.id', 'c.persons_id')
            ->leftJoin(
                'children_requests as cr',
                function (JoinClause $join) use ($periodId) {
                    $join->where([
                        'cr.children_id' => DB::raw('c.id'),
                        'cr.periods_id' => $periodId,
                        'cr.deleted_at' => null,
                    ]);
                }
            )->leftJoin('education_levels as el', 'el.id', 'cr.education_levels_id')
            ->leftJoin('plants as ct', 'ct.id', 'cr.plants_id')
            ->where([
                'p.deleted_at' => null,
                'c.deleted_at' => null,
            ]);

        if (Users::ROLE_USER === $this->role) {
            $builder->where('p.id', $this->personId);
        }

        return $builder->disablePagination()->collection();
    }

    public function getChildrenFullRequests(int $personRequestId): Collection
    {
        $builder = Builder::table('children_requests', 'cr')
            ->schema($this->schemaChildrenFullRequests())
            ->join('children as c', 'c.id', 'cr.children_id')
            ->join('persons as p', 'p.id', 'c.persons_id')
            ->join('education_levels as el', 'el.id', 'cr.education_levels_id')
            ->leftJoin('plants as ct', 'ct.id', 'cr.plants_id')
            ->where([
                'p.deleted_at' => null,
                'c.deleted_at' => null,
                'cr.deleted_at' => null,
                'cr.persons_requests_id' => $personRequestId,
                'cr.periods_id' => $this->periodId,
            ]);

        if (Users::ROLE_ADMIN == $this->role) {
            $builder->where('p.responsable', '&&', array_to_pgarray([$this->personId]));
        }

        return $builder->disablePagination()
        ->collection()
        ->each(function (&$item) {
            $item->documents = json_decode($item->documents, true);
            $item = (array) $item;
        });
    }

    public function getChildrenRequestsExcel(int $periodId): Collection
    {
        $builder = Builder::table('children_requests', 'cr')
        ->schema($this->schemaChildrenRequestsExcel())
        ->join('children as c', 'c.id', 'cr.children_id')
        ->join('persons_requests as pr', 'pr.id', 'cr.persons_requests_id')
        ->join('persons as p', 'p.id', 'c.persons_id')
        ->join('units as u', 'u.id', 'p.units_id')
        ->join('education_levels as el', 'el.id', 'cr.education_levels_id')
        ->join('periods as pe', 'pe.id', 'cr.periods_id')
        ->leftJoin('boats as b', 'b.id', 'p.boats_id')
        ->leftJoin('plants as pl', 'pl.id', 'cr.plants_id')
        ->leftJoin('districts as dt', 'dt.id', 'cr.districts_id')
        ->leftJoin('provinces as pv', 'pv.id', 'dt.provinces_id')
        ->leftJoin('departments as dp', 'dp.id', 'pv.departments_id')
        ->leftJoin('persons as ap', 'ap.id', 'pr.approved_by')
        ->where([
            'p.deleted_at' => null,
            'c.deleted_at' => null,
            'cr.deleted_at' => null,
            'pr.deleted_at' => null,
            'pr.periods_id' => $periodId,
        ]);

        if (Users::ROLE_ADMIN == $this->role) {
            $builder->where('p.responsable', '&&', array_to_pgarray([$this->personId]));
        }

        if ($this->units) {
            $builder->whereIn('u.id', $this->units);
        }

        if ($this->boats) {
            $builder->whereIn('b.id', $this->boats);
        }

        if ($this->status) {
            $builder->whereIn('pr.status', $this->status);
        }

        return $builder->disablePagination()->collection();
    }

    public function schemaChildrenRequests(): array
    {
        return [
            'id',
            'name',
            'paternal_surname',
            'maternal_surname',
            'gender',
            'birth_date',
            'fullname' => DB::raw('TRIM(c.name || \' \' || c.paternal_surname || \' \' || c.maternal_surname)'),
            'parent:p' => [
                'id',
                'names',
                'gender',
                'dni',
            ],
            'request:cr' => [
                'id',
                'get_loan',
                'get_pack',
                'status',
                'plant:ct' => [
                    'id',
                    'name',
                ],
                'education_level:el' => [
                    'id',
                    'name',
                    'amount',
                ],
            ],
            'created_at',
        ];
    }

    public function schemaChildrenFullRequests(): array
    {
        return [
            'id',
            'get_loan',
            'get_pack',
            'status',
            'responsable_name',
            'responsable_dni',
            'responsable_phone',
            'address',
            'address_reference',
            'delivery_type',
            'status',
            'child:c' => [
                'id',
                'name',
                'paternal_surname',
                'maternal_surname',
                'gender',
                'birth_date',
                'fullname' => DB::raw('TRIM(c.name || \' \' || c.paternal_surname || \' \' || c.maternal_surname)'),
            ],
            'documents' => $this->sqChildrenDocuments(),
            'plant:ct' => [
                'id',
                'name',
            ],
            'education_level:el' => [
                'id',
                'name',
                'amount',
            ],
            'created_at',
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
                        'cd.updated_at as last_update'
                    )->from('children_documents', 'cd')
                    ->where('cd.children_requests_id', DB::raw('cr.id'))
                    ->whereNull('cd.deleted_at')
                    ->orderBy('cd.id', 'asc');
                }, 'sq');
        };
    }

    private function schemaChildrenRequestsExcel(): array
    {
        return [
            'get_loan',
            'delivery_type',
            'address',
            'address_reference',
            'responsable_name',
            'responsable_dni',
            'responsable_phone',
            'period:pe' => ['amount_bonds', 'max_amount_loan'],
            'request_date' => DB::raw('date(pr.created_at)'),
            'approved_date' => DB::raw('date(pr.updated_at)'),
            'register_child' => DB::raw('date(cr.created_at)'),
            'approved:ap' => [
                'by' => 'ap.names',
                'at' => DB::raw('date(pr.approved_at)'),
            ],
            'status' => 'pr.status',
            'education_level:el' => ['id', 'name', 'amount'],
            'department:dp' => ['name'],
            'province:pv' => ['name'],
            'district:dt' => ['name'],
            'unit:u' => ['name'],
            'boat:b' => ['name'],
            'plant:pl' => ['name'],
            'has_superior_child' => DB::raw('fn_has_superior_child(p.id)'),
            'person:p' => ['names', 'dni', 'phone', 'id'],
            'child:c' => [
                'fullname' => DB::raw('TRIM(c.paternal_surname || \' \' || c.maternal_surname || \' \' || c.name)'),
                'birth_date',
            ],
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
