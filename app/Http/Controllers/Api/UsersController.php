<?php

namespace App\Http\Controllers\Api;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Repositories\UsersRepository;

class UsersController extends Controller
{
    /**
     * @var UsersRepository
     */
    private $repository;

    public function __construct(UsersRepository $repository)
    {
        $this->repository = $repository;
    }

    public function show(int $id)
    {
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
