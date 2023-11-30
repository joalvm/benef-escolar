<?php

namespace App\Http\Controllers\Api;

use Joalvm\Utils\Response;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Repositories\SessionRepository;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    /**
     * @var SessionRepository
     */
    private $repository;

    public function __construct(SessionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login(Request $request)
    {
        $session = $this->repository->login($request);

        session([
            'loggedIn' => true,
            'user' => $session->user,
        ]);

        return Response::created($session->token, trans('session.authorized'));
    }
}
