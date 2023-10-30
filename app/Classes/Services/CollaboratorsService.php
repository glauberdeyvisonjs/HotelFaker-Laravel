<?php

namespace App\Classes\Services;

use App\Models\Collaborators;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CollaboratorsService
{
    /**
     * @return Collection|array
     */
    public function list(): Collection|array
    {
        return Collaborators::query()->whereNull('deleted_at')->get();
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
     * @return JsonResponse
     * @throws Exception
     */
    public function store($data): JsonResponse
    {
        $increment = $this->getIncrement();
        $user = $this->getUser($data);

        if (isset($user)) {
            try {
                return Collaborators::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'cpf' => $user->cpf,
                    'cod_user' => $user->id,
                    'registration' => $increment,
                    'password' => Hash::make($data->password),
                    'flag_permissions' => $data->flag_permissions ?? '0',
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 400);
            }
        } else {
            throw new Exception('Não foi possível encontrar o usuário!', 404);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $collaborator = Collaborators::query()->where('id', $id)->first();

        if (!isset($collaborator)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este usuário nunca foi vinculado!',
            ], 404);
        } else {
            if (isset($collaborator->deleted_at)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Este usuário foi desvinculado em ' . Carbon::createFromFormat('Y-m-d H:i:s', $collaborator->deleted_at)->format('d/m/Y') . '!',
                ], 404);
            } else {
                return response()->json([
                    'status' => 'success',
                    'collaborator' => $collaborator,
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function destroy($id): void
    {
        Collaborators::query()->where('id', $id)->update([
            'deleted_at' => Carbon::now(),
        ]);
    }
}
