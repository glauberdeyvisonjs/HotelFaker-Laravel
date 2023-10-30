<?php

namespace App\Classes\Services;

use App\Models\Collaborator;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CollaboratorService
{
    /**
     * @return Collection|array
     */
    public function list(): Collection|array
    {
        return Collaborator::query()->whereNull('deleted_at')->get();
    }

    /**
     * @return string
     */
    public function getIncrement(): string
    {
        $increment = DB::table('collaborators')->orderBy('id', 'DESC')->first('registration');

        if (isset($increment)) {
            $increment = strval($increment->registration + 1);
            $count = strlen($increment);

            $sub = 8 - $count;

            for ($i = 0; $i < $sub; $i++) {
                $increment = '0' . $increment;
            }
        } else {
            $increment = '00000001';
        }

        return $increment;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getUser($data): mixed
    {
        $checkUser = User::query()->where('email', $data->email)->first();

        if (isset($checkUser)) {
            User::query()->where('id', $checkUser->id)
                ->update([
                    'flag_collaborator' => '1',
                    'deleted_at' => null,
                ]);

            $user = User::query()->where('id', $checkUser->id)->first();
        } else {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'cpf' => $data->cpf,
                'password' => Hash::make($data->password),
                'flag_collaborator' => '1',
            ]);
        }

        return $user;
    }

    /**
     * @param $data
     * @return Model
     * @throws Exception
     */
    public function store($data): Model
    {
        $increment = $this::getIncrement();
        $user = $this->getUser($data);

        if (isset($user)) {
            try {
                return Collaborator::query()
                    ->create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'cpf' => $user->cpf,
                        'cod_user' => $user->id,
                        'registration' => $increment,
                        'password' => Hash::make($data->password),
                        'flag_permissions' => $data->flag_permissions ?? '0',
                    ]);
            } catch (Exception $e) {
                throw new Exception('Não foi possível cadastrar o colaborador!', 500);
            }
        } else {
            throw new Exception('Não foi possível encontrar o usuário!', 404);
        }
    }

    /**
     * @param $id
     * @return array
     * @throws Exception
     */
    public function show($id): array
    {
        $collaborator = Collaborator::query()->where('id', $id)->first();

        if (!isset($collaborator)) {
            throw new Exception('Não foi possível encontrar o colaborador!', 404);
        } else {
            if (isset($collaborator->deleted_at)) {
                throw new Exception('Este usuário foi desvinculado em ' . Carbon::createFromFormat('Y-m-d H:i:s', $collaborator->deleted_at)->format('d/m/Y') . '!', 404);
            } else {
                $permissao = match ($collaborator->flag_permissions) {
                    1 => 'Administrador',
                    2 => 'SuperUser',
                    default => 'Colaborador',
                };

                return [
                    'id' => $collaborator->id,
                    'name' => $collaborator->name,
                    'email' => $collaborator->email,
                    'cpf' => $collaborator->cpf,
                    'registration' => $collaborator->registration,
                    'flag_permissions' => $permissao,
                ];
            }
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function destroy($id): void
    {
        Collaborator::query()->where('id', $id)->update([
            'deleted_at' => Carbon::now(),
        ]);
    }
}
