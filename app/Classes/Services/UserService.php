<?php

namespace App\Classes\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UserService
{
    public function list()
    {
        //
        return User::select('id', 'name', 'email', 'cpf', 'flag_collaborator')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
    }

    /**
     * @param $id
     * @return array
     * @throws Exception
     */
    public function show($id): array
    {
        $user = User::where('id', $id)->first();

        if (!isset($user)) {
            throw new ModelNotFoundException('O usuário não existe!');
        } else {
            if (isset($user->deleted_at)) {
                throw new Exception('O usuário foi deletado!', 404);
            } else {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cpf' => $user->cpf,
                    'flag_collaborator' => $user->flag_collaborator == 1 ? 'Sim' : 'Não',
                ];
            }
        }
    }

    /**
     * @param $data
     * @param $id
     * @return JsonResponse
     */
    public function update($data, $id): JsonResponse
    {
        $user = User::where('id', $id)->first();

        if (!isset($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'O usuário não existe!',
            ], 404);
        } else {
            if (isset($user->deleted_at)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'O usuário foi deletado!',
                ], 404);
            } else {
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->cpf = $data['cpf'];
                $user->flag_collaborator = $data['flag_collaborator'];
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'O usuário foi atualizado com sucesso!',
                ]);
            }
        }
    }

    public function destroy($id): void
    {
        User::query()
            ->where('id', $id)
            ->update([
                'deleted_at' => Carbon::now(),
            ]);
    }
}
