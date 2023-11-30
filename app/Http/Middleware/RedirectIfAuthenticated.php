<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $isLoggedIn = session('isLoggedIn', false);
        $sessionRole = session('user.role', null);

        if (!$isLoggedIn and !$this->isPublicPath()) {
            return redirect('login');
        }

        if ($isLoggedIn and $this->isPublicPath()) {
            return redirect(
                RedirectBasedOnYourRole::DEFAULT_ROUTES[$sessionRole]
            );
        }

        return $next($request);
    }

    private function isPublicPath(): bool
    {
        $another = preg_match(
            "/^email-verification\/([0-9]+)\/(([A-Za-z0-9\-\_]*)\.([A-Za-z0-9\-\_]*)\.([A-Za-z0-9\-\_]*))$/",
            request()->path()
        );

        return in_array(request()->path(), ['login', 'register', '/']) || $another;
    }
}
