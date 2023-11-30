<?php

namespace App\Http\Controllers\Api;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Repositories\EducationLevelsRepository;

class EducationLevelsController extends Controller
{
    /**
     * @var EducationLevelsRepository
     */
    private $repository;

    public function __construct(EducationLevelsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return Response::collection(
            $this->repository->all()
        );
    }

    public function store(Request $request)
    {
        $this->repository->setUser(config('user'));

        return Response::created(
            $this->repository->find(
                $this->repository->save($request)->id
            )
        );
    }

    public function show(int $id)
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

    public function destroy(int $id)
    {
        $this->repository->setUser(config('user'));

        return Response::destroyed(
            ['deleted' => $this->repository->delete($id)]
        );
    }
}
