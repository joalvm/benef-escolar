<?php

namespace App\Http\Controllers\Views;

use Exception;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Repositories\SessionRepository;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Middleware\RedirectBasedOnYourRole;

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
        return view('pages.login.login');
    }

    public function logout()
    {
        Session::flush();

        return Redirect::to('/login');
    }

    public function loginCheck(Request $request)
    {
        try {
            $session = $this->repository->login($request);
            $prefix = 'super_admin' == $session->user->role ? 'admin' : $session->user->role;

            session([
                'isLoggedIn' => true,
                'user' => (array) $session->user,
                'api_token' => (array) $session->token,
            ]);

            return redirect(
                RedirectBasedOnYourRole::DEFAULT_ROUTES[$session->user->role]
            );
        } catch (Exception $ex) {
            return redirect('/login')->with([
                'status' => 'error',
                'status_message' => $ex->getMessage(),
            ]);
        }
    }
}
