<?php

namespace App\Http\Controllers\Api\Persons;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use Illuminate\Support\Facades\DB;
use App\Repositories\PeriodsRepository;
use App\Repositories\Persons\RequestsRepository;
use App\Repositories\Persons\DocumentsRepository;
use App\Repositories\Custom\PersonsRepository as CustomPersonsRepository;

class RequestsController extends Controller
{
    private $repository;

    private $customPersonsRepository;

    public function __construct(
        RequestsRepository $repository,
        CustomPersonsRepository $customPersonsRepository
    ) {
        $this->repository = $repository;
        $this->customPersonsRepository = $customPersonsRepository;
    }

    public function index(Request $request): Response
    {
        $this->repository->setUser(config('user'));
        $this->repository->setPeriod(config('app.period_id'));

        return Response::collection(
            $this->repository
                ->setPersons($request->get('persons'))
                ->setResponsable($request->get('responsable'))
                ->setStatus($request->get('status'))
                ->setBoats($request->get('boats'))
                ->setUnits($request->get('units'))
                ->all()
        );
    }

    public function show(int $id): Response
    {
        $this->repository->setUser(config('user'));

        return Response::item(
            $this->repository->find($id)
        );
    }

    public function getCounter(Request $request): Response
    {
        $this->customPersonsRepository->setUser(config('user'));
        $this->customPersonsRepository->setPeriod(config('app.period_id'));

        return Response::collection(
            $this->customPersonsRepository
                ->setBoats($request->get('boats'))
                ->setUnits($request->get('units'))
                ->setStatus($request->get('status'))
                ->getCountsRequests()
        );
    }

    public function store(Request $request)
    {
        $period = new PeriodsRepository();

        $this->repository->setUser(config('user'));

        DB::beginTransaction();

        $request->merge(['periods_id' => $period->getActive()->get('id')]);

        $model = $this->repository->save($request);

        if ($request->has('documents')) {
            $documentRepository = new DocumentsRepository();

            foreach ($request->input('documents') as $document) {
                unset($document['id']);

                $documentRepository->save(new Request(
                    array_merge(
                        ['persons_requests_id' => $model->id],
                        $document
                    )
                ));
            }
        }

        DB::commit();

        return Response::created($this->repository->find($model->id));
    }

    public function destroy(int $id)
    {
        $this->repository->setUser(config('user'));

        return Response::destroyed([
            'deleted' => $this->repository->delete($id),
        ]);
    }
}
