<?php

namespace App\Repositories;

use Closure;
use DateTime;
use stdClass;
use App\Models\Users;
use Firebase\JWT\JWT;
use Joalvm\Utils\Item;
use App\Models\Sessions;
use Joalvm\Utils\Builder;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Components\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UnauthorizedException;

class SessionRepository extends Repository
{
    public function profile(int $userId): Item
    {
        return Builder::table('public.users', 'u')
            ->schema($this->profileSchema())
            ->setCasts($this->profileCasts())
            ->where([
                'u.deleted_at' => null,
                'u.enabled' => true,
                'u.id' => $userId,
            ])->item();
    }

    public function login(Request $request): stdClass
    {
        $user = $this->getUserInfo($request);

        $token = $this->buildToken($user);

        $this->saveSession($user->get('id'), $token);

        $token->expire = $token->expire->timestamp * 1000;

        return (object) [
            'token' => $token,
            'user' => (object) [
                'user_id' => $user->get('id'),
                'person_id' => $user->get('persons_id'),
                'names' => $user->get('names'),
                'dni' => $user->get('dni'),
                'role' => $user->get('role'),
                'enabled' => $user->get('enabled'),
                'isVerified' => $user->get('is_verified'),
            ],
        ];
    }

    public function saveSession(int $uid, stdClass $jwt): Sessions
    {
        $agent = new Agent();

        $browser = $agent->browser();
        $version = $agent->version($browser);
        $platform = $agent->platform();
        $ip = request()->ip();

        Sessions::where([
            'users_id' => $uid,
            'closed_at' => null,
        ])->update([
            'closed_at' => (new DateTime())->format(DateTime::ISO8601),
        ]);

        $model = new Sessions([
            'users_id' => $uid,
            'token' => $jwt->token,
            'expire' => $jwt->expire,
            'ip' => $ip,
            'browser' => $browser,
            'version' => $version,
            'platform' => $platform,
        ]);

        $model->save();

        return $model;
    }

    private function buildToken(Item $user, int $expireHours = 72): stdClass
    {
        $expire = Carbon::now()->addRealHours($expireHours);

        $payload = [
            'uid' => $user->get('id'),
            'pid' => $user->get('persons_id'),
            'rol' => $user->get('role'),
            'uip' => request()->ip(),
            'iat' => Carbon::now()->timestamp,
            'exp' => $expire->timestamp,
        ];

        $token = JWT::encode($payload, config('app.key'));

        return (object) [
            'token' => $token,
            'expire' => $expire,
        ];
    }

    private function getUserInfo(Request $request): Item
    {
        // Validando Inputs
        $request->validate([
            'dni' => ['required', 'string', 'size:8'],
            'password' => ['required', 'string', 'max:50'],
        ]);

        // Verificando la existencia del usuario
        $user = $this->findUser($request->input('dni'));

        // Verificando contraseña ingresada.
        if (!$this->isValidPassword($user, $request->input('password'))) {
            throw new UnauthorizedException(trans('auth.session_wrong'));
        }

        if (!$user->get('enabled')) {
            throw new UnauthorizedException(trans('auth.session_disabled'));
        }

        return $user;
    }

    private function findUser(string $dni): Item
    {
        $query = DB::table('users', 'u')
            ->select([
                'u.id',
                'p.id AS persons_id',
                'p.names',
                'p.dni',
                'u.password',
                'u.salt',
                'u.role',
                'u.enabled',
                DB::raw('(u.verified_at IS NOT NULL)::bool as is_verified'),
            ])->join('persons as p', 'p.id', 'u.persons_id')
            ->where('p.dni', $dni)
            //->where('u.role', '<>', Users::ROLE_USER)
            ->whereNull('u.deleted_at')
            ->whereNull('p.deleted_at')
            ->first();

        if (is_null($query)) {
            throw new UnauthorizedException(trans('auth.session_wrong'));
        }

        return new Item($query, [
            'id', 'persons_id', 'names', 'dni', 'password', 'salt', 'role', 'enabled', 'is_verified',
        ]);
    }

    private function isValidPassword(Item $user, string $inputPassword): bool
    {
        $userPassword = $user->get('password');
        $salt = $user->get('salt');

        return Hash::check("${salt}.${inputPassword}", $userPassword ?? '');
    }

    private function profileCasts(): Closure
    {
        return function ($item) {
            cast_json($item, ['shopping_cart']);

            return $item;
        };
    }

    private function profileSchema(): array
    {
        return [
            'id',
            'name',
            'lastname',
            'company_name',
            'email',
            'image_url',
            'enabled',
            'is_verified' => DB::raw('(verified_at IS NOT NULL)::bool'),
        ];
    }
}
