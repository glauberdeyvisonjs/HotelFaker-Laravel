<?php

namespace App\Classes\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserService
{
    /**
     * @return Builder[]|Collection
     */
    public function list(): Collection|array
    {
        return User::query()->select('id', 'name', 'email', 'cpf', 'flag_collaborator')
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
        $user = User::query()->where('id', $id)->first();

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
     * @param Request $request
     * @param $id
     * @return void
     * @throws Exception
     */
    public function update(Request $request, $id): void
    {
        $user = User::query()->where('id', $id)->first();

        if (!isset($user)) {
            throw new ModelNotFoundException('O usuário não existe!');
        } else {
            if (isset($user->deleted_at)) {
                throw new Exception('O usuário foi deletado!', 404);
            } else {
                User::query()
                    ->where('id', $id)
                    ->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'cpf' => $request->cpf,
                        'flag_collaborator' => $request->flag_collaborator,
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
