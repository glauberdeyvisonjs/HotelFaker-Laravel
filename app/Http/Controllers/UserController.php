<?php

namespace App\Http\Controllers;

use App\Classes\Services\UserService;
use App\Classes\Support\HelperReturn;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    protected UserService $userService;

    /**
     * UserService constructor.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $list = $this->userService->list();
            return HelperReturn::returnSuccess('users', $list);
        } catch (Exception $e) {
            return HelperReturn::returnException($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Object
     */
    public function show(int $id): object
    {
        try {
            return HelperReturn::returnSuccess('user', $this->userService->show($id));
        } catch (Exception|ModelNotFoundException|NotFoundHttpException|MethodNotAllowedHttpException|HttpException $e) {
            return HelperReturn::returnException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->userService->update($request->all(), $id);

            return HelperReturn::returnSuccess('user', $this->userService->show($id), 'Usuário atualizado com sucesso!');
        } catch (ModelNotFoundException|NotFoundHttpException|MethodNotAllowedHttpException|HttpException|Exception $e) {
            return HelperReturn::returnException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->destroy($id);
            return HelperReturn::returnSuccess('user', null, 'Usuário excluído com sucesso!');
        } catch (Exception $e) {
            return HelperReturn::returnException($e);
        }
    }

    public static function sendMail($user): bool
    {
        try {
            Mail::send('mail.emailVerification',
                ['user' => $user],
                function ($message) use ($user) {
                    try {
                        $message->bcc($user->email, $user->name)
                            ->subject('Seja bem vindo ao Hotel Faker, ' . $user->name . '!');
                        return true;
                    } catch (Exception) {
                        return false;
                    }
                });
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
