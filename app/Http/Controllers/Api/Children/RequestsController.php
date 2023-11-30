<?php

namespace App\Http\Controllers\Api\Children;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Repositories\PeriodsRepository;
use App\Repositories\Children\RequestsRepository;

class RequestsController extends Controller
{
    /**
     * @var RequestsRepository
     */
    private $repository;

    public function __construct(RequestsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $this->repository->setUser(config('user'));

        return Response::collection(
            $this->repository
                ->setPersons($request->get('persons'))
                ->setChildren($request->get('children'))
                ->setStatus($request->get('status'))
                ->setPlants($request->get('plants'))
                ->setResponsable($request->get('responsable'))
                ->all()
        );
    }

    public function store(Request $request): Response
    {
        $periodRepository = new PeriodsRepository();

        $this->repository->setUser(config('user'));

        if (!$request->has('periods_id')) {
            $request->merge([
                'periods_id' => $periodRepository->getActive()->get('id'),
            ]);
        }

        return Response::created(
            $this->repository->find(
                $this->repository->save($request)->id
            )
        );
    }

    public function show(int $id): Response
    {
        $this->repository->setUser(config('user'));

        return Response::item($this->repository->find($id));
    }

    public function update(int $id, Request $request)
    {
        $this->repository->setUser(config('user'));

        return Response::updated(
            $this->repository->find(
                $this->repository->update($id, $request)->id
            )
        );
    }

    public function destroy($id)
    {
        $this->repository->setUser(config('user'));

        return Response::updated([
            'deleted' => $this->repository->delete($id),
        ]);
    }
}
