<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Users;
use Illuminate\Support\Facades\Config;

class RedirectBasedOnYourRole
{
    public const DEFAULT_ROUTES = [
        Users::ROLE_USER => 'user/bonds',
        Users::ROLE_ADMIN => 'admin/dashboard',
        Users::ROLE_SUPER_ADMIN => 'admin/dashboard',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = array_slice(func_get_args(), 2);
        $sessionRole = session('user.role', null);

        $currRole = in_array($sessionRole, $roles);

        if (Users::ROLE_SUPER_ADMIN !== $sessionRole and !$currRole) {
            session()->flash('status', 'error');
            session()->flash(
                'status_message',
                'No tiene permisos de acceso para acceder a la ruta.'
            );

            return redirect(self::DEFAULT_ROUTES[$sessionRole]);
        }

        if (in_array(Users::ROLE_USER, $roles)) {
            Config::set('app.period_id', to_int($request->header('period')));
        }

        return $next($request);
    }

    private function isPublicPath(): bool
    {
        return in_array(request()->path(), ['login', 'register', '/']);
    }
}
