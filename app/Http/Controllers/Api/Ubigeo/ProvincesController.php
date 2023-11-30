<?php

namespace App\Http\Controllers\Api\Ubigeo;

use Joalvm\Utils\Response;
use App\Components\Controller;
use App\Repositories\Ubigeo\ProvincesRepository;
use Illuminate\Http\Request;

class ProvincesController extends Controller
{
    private $repository;

    public function __construct(ProvincesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        return Response::collection(
            $this->repository
                ->setDepartments($request->get('departments'))
                ->all()
        );
    }

    public function show(int $id): Response
    {
        return Response::item($this->repository->find($id));
    }
}
