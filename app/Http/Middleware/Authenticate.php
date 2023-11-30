<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Config;
use App\Exceptions\UnauthorizedException;

class Authenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = JWT::decode(
                $request->bearerToken() ?? '',
                config('app.key'),
                ['HS256']
            );

            Config::set([
                'app.period_id' => to_int($request->header('period')),
                'user.id' => $token->uid,
                'user.person_id' => $token->pid,
                'user.role' => $token->rol,
            ]);
        } catch (Exception $ex) {
            throw new UnauthorizedException($ex->getMessage());
        }

        return $next($request);
    }
}
