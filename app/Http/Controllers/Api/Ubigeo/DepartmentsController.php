<?php

namespace App\Http\Controllers\Api\Ubigeo;

use Joalvm\Utils\Response;
use App\Components\Controller;
use App\Repositories\Ubigeo\DepartmentsRepository;

class DepartmentsController extends Controller
{
    private $repository;

    public function __construct(DepartmentsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): Response
    {
        return Response::collection($this->repository->all());
    }

    public function show(int $id): Response
    {
        return Response::item($this->repository->find($id));
    }
}
