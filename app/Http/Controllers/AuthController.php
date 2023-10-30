<?php

namespace App\Http\Controllers;

use App\Classes\Services\UserService;
use App\Classes\Support\HelperReturn;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected UserService $userService;

    /**
     * userService constructor.
     *
     * @return UserService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        return $userService;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'user' => $this->userService->show($user->id),
            'authorization' => $this->respondWithToken($token)
        ]);
    }

    /**
     * Register a User.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
                'cpf' => 'required|string|min:11|max:11',
            ]);

            $existeEmail = User::query()->where('email', $request->email)->first();
            $existeCpf = User::query()->where('cpf', $request->cpf)->first();

            if (isset($existeEmail) && !isset($existeEmail->deleted_at)) {
                throw new Exception('O email já está cadastrado.', 409);
            } else if (isset($existeCpf) && !isset($existeCpf->deleted_at)) {
                throw new Exception('O CPF já está cadastrado.', 409);
            } else {
                if (isset($existeEmail->deleted_at)) {
                    $existeEmail->deleted_at = null;
                    $existeEmail->save();
                    $userReactivated = true;

                    $user = $existeEmail;
                } else if (isset($existeCpf->deleted_at)) {
                    $existeCpf->deleted_at = null;
                    $existeCpf->save();
                    $userReactivated = true;

                    $user = $existeCpf;
                } else {
                    User::query()->create([
                        'name' => trim($request->name),
                        'email' => trim($request->email),
                        'password' => Hash::make(trim($request->password)),
                        'cpf' => trim($request->cpf),
                    ]);

                    $userReactivated = false;

                    $user = User::query()->where('email', trim($request->email))->first();
                }
            }
        } catch (Exception $e) {
            return HelperReturn::returnException($e);
        }

        $token = Auth::login($user);

        try {
            UserController::sendMail($user);

            if ($userReactivated) {
                return response()->json([
                    'message' => 'Usuário reativado com sucesso, email de confirmação enviado.',
                    'user' => $this->userService->show($user->id),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => 'Usuário criado com sucesso, email de confirmação enviado.',
                    'user' => $this->userService->show($user->id),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }
        } catch (Exception) {
            if ($userReactivated) {
                return response()->json([
                    'message' => 'Usuário reativado com sucesso, email de confirmação não enviado.',
                    'user' => $this->userService->show($user->id),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => 'Usuário criado com sucesso, email de confirmação não enviado.',
                    'user' => $this->userService->show($user->id),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return response()->json([
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return array
     */
    protected function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
        ];
    }
}
