<?php

namespace App\Repositories;

use Closure;
use DateTime;
use stdClass;
use App\Models\Users;
use Firebase\JWT\JWT;
use Joalvm\Utils\Item;
use Joalvm\Utils\Builder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Components\Repository;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UnprocessableEntityException;

class UsersRepository extends Repository
{
    private static $notAllowedKeys = [
        'salt',
        'recovery_token',
        'verification_token',
        'verified_at',
    ];

    public function find(int $id): Item
    {
        return $this->builder()->where('u.id', $id)->item();
    }

    public function save(Request $request): Users
    {
        $model = new Users($request->except(self::$notAllowedKeys));

        if (!$model->getAttribute('password')) {
            $model->setAttribute('password', Str::random(8));
        }

        $model->setAttribute('enabled', true);

        $model->setTempPassword($model->password);

        $model->validate();

        $cryto = $this->crytoPassword($model->password);

        $model->setAttribute('salt', $cryto->salt);
        $model->setAttribute('password', $cryto->password);

        $model->save();

        $this->createVerificationToken($model);

        return $model;
    }

    public function update(int $id, Request $request): Users
    {
        if (Users::ROLE_USER === $this->role) {
            if ($id !== $this->userId) {
                throw new ForbiddenException('No tiene permiso para esta acción');
            }

            $id = $this->userId;
        }

        $model = self::getModel($id);

        // 1. Verificar si la contraseña a cambiado
        if ($request->has('password')) {
            $auth = $this->changeIfPasswordsDifferent($model, $request);
            $model->password = $auth->password;
            $model->salt = $auth->salt;
        }

        $model->fill($request->except(self::$notAllowedKeys));

        $model->validate()->update();

        return $model;
    }

    public static function getModel(int $id): Users
    {
        /** @var Users $model */
        $model = Users::find($id);

        if (!$model) {
            throw new NotFoundException('validation.resource.not_found');
        }

        return $model;
    }

    private function changeIfPasswordsDifferent(Users $model, Request &$request): stdClass
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'required_with:current_password,confirm_password'],
            'confirm_password' => ['required', 'string', 'same:password'],
        ]);

        $password = "{$model->salt}.{$request->input('current_password')}";

        if (!Hash::check($password, $model->password, ['rounds' => 14])) {
            throw new UnprocessableEntityException(
                ['current_password' => [trans('validation.password_wrong')]]
            );
        }

        self::$notAllowedKeys[] = 'password';

        return $this->crytoPassword(
            $request->input('password'),
            Str::random()
        );
    }

    private function createVerificationToken(Users $model): void
    {
        if (!is_null($model->verified_at)) {
            throw new UnprocessableEntityException(
                trans('validation.exists'),
                ['verified_at' => $model->verified_at]
            );
        }

        $person = $model->persons()->first();

        $model->fill([
            'verification_token' => JWT::encode([
                'pid' => $person->id,
                'iat' => (int) (new DateTime('now'))->format('U'),
            ], config('app.key')),
        ]);

        $model->update();
    }

    private function crytoPassword(string $password, ?string $salt = null): stdClass
    {
        $salt = $salt ?? Str::random();
        $model = new stdClass();

        $model->real = $password;
        $model->password = Hash::make("${salt}.${password}", ['rounds' => 14]);
        $model->salt = $salt;

        return $model;
    }

    public function builder(): Builder
    {
        return Builder::table('users', 'u')
            ->schema($this->schema())
            ->join('persons as p', 'p.id', 'u.persons_id')
            ->where([
                'p.deleted_at' => null,
                'u.deleted_at' => null,
            ])->setFilters($this->filters());
    }

    public function filters(): Closure
    {
        return function (Builder $builder) {
            if (Users::ROLE_USER === $this->role) {
                $builder->where('u.id', $this->userId);
            }

            return $builder;
        };
    }

    private function schema(): array
    {
        return [
            'id',
            'last_login',
            'verified_at',
            'person:p' => [
                'id',
                'names',
                'dni',
                'gender',
            ],
            'created_at',
        ];
    }
}
