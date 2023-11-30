<?php

namespace App\Http\Controllers\Api;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Repositories\PersonsRepository;

class PersonsController extends Controller
{
    /**
     * @var PersonsRepository
     */
    private $repository;

    public function __construct(PersonsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $this->repository->setUser(config('user'));

        return Response::collection($this->repository->all());
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
}
