<?php

namespace App\Http\Controllers\Views;

use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Components\Controller;
use App\Repositories\RegisterRepository;

class RegisterController extends Controller
{
    public function index()
    {
        return view('pages.register.register');
    }

    public function verification(int $id, string $token, Request $request)
    {
        try {
            $tokenObj = JWT::decode($token, config('app.key'), ['HS256']);

            if ($tokenObj->pid !== $id) {
                throw new Exception('El link de verificación es erroneo.');
            }

            $repository = new RegisterRepository();

            if (!$repository->verifiedEmail($id, $token)) {
                throw new Exception('No sea ha podido verificar su acceso.');
            }

            return redirect('/login')->with([
                'status' => 'success',
                'status_message' => 'Su acceso a sido verificado, puede ingresar a la plataforma',
            ]);
        } catch (Exception $ex) {
            return redirect('/login')->with([
                'status' => 'error',
                'status_message' => $ex->getMessage(),
            ]);
        }
    }
}
