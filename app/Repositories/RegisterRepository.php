<?php

namespace App\Repositories;

use DateTime;
use App\Models\Users;
use App\Models\Persons;
use Joalvm\Utils\Builder;
use Illuminate\Http\Request;
use App\Components\Repository;
use App\Exceptions\UnprocessableEntityException;

class RegisterRepository extends Repository
{
    public function getByDNI(string $dni)
    {
        return Builder::table('persons', 'p')
            ->schema(['id', 'names', 'gender', 'status'])
            ->where([
                'p.deleted_at' => null,
                'p.dni' => $dni,
            ])->item();
    }

    public function registerEmail(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
            'email' => ['required', 'string', 'email'],
        ]);

        $personModel = $this->updatePerson($request);
        $userRepository = new UsersRepository();

        $personModel->users = $userRepository->save(new Request([
            'persons_id' => $personModel->id,
            'role' => Users::ROLE_USER,
            'password' => (string) $personModel->id,
        ]));

        return $personModel;
    }

    public function verifiedEmail(int $personId, string $token): bool
    {
        $verifiedAt = (new DateTime())->format(DateTime::ISO8601);
        $person = Persons::with('users')->find($personId);

        if ($person->users->verification_token != $token) {
            return false;
        }

        if (Persons::STATUS_VERIFIED == $person->status) {
            return true;
        }

        $person->setAttribute('status', Persons::STATUS_VERIFIED)->update();
        $person->users->setAttribute('verified_at', $verifiedAt)->update();

        return true;
    }

    private function updatePerson(Request $request): Persons
    {
        /** @var Persons $model */
        $model = Persons::with('users')->find($request->input('id'));

        if (Persons::STATUS_REGISTERED == $model->status) {
            throw new UnprocessableEntityException(
                'Sus credenciales ya fueron enviadas al correo electrónico.'
            );
        } elseif (Persons::STATUS_VERIFIED == $model->status) {
            throw new UnprocessableEntityException(
                'Su proceso de registro a concluido, usted puede ingresar a la plataforma.'
            );
        }

        $model->setAttribute('email', $request->input('email'));
        $model->setAttribute('status', Persons::STATUS_REGISTERED);

        $model->update();

        return $model;
    }
}
