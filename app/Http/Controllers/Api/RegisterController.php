<?php

namespace App\Http\Controllers\Api;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Mail\CredentialsMailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\NotFoundException;
use App\Repositories\RegisterRepository;

class RegisterController extends Controller
{
    /**
     * @var RegisterRepository
     */
    private $repository;

    public function __construct(RegisterRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDNI(string $dni)
    {
        $item = $this->repository->getByDNI($dni);

        if ($item->isEmpty()) {
            throw new NotFoundException('El DNI no se encuentra registrado.');
        }

        return Response::item($item);
    }

    public function registerEmail(Request $request)
    {
        DB::beginTransaction();

        /** @var \App\Models\Persons $model */
        $model = $this->repository->registerEmail($request);

        Mail::to($model->email)->send(new CredentialsMailable($model));

        DB::commit();

        return Response::created($model);
    }
}
