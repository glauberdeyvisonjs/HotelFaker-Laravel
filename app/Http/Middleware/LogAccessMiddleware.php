<?php

namespace App\Http\Middleware;

use App\Models\LogAcess;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse|JsonResponse|Closure
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse|Closure
    {
        $ip = $request->server->get('REMOTE_ADDR');
        $rota = $request->getRequestUri();
        $authorizedRoutes = ['/api/login', '/api/register', '/api/logout', '/api/refresh'];

        $data = [
            'ip' => $ip,
            'route' => $rota,
        ];

        if (!in_array($rota, $authorizedRoutes)) {
            $user = JWTAuth::parseToken()->authenticate();
            $data['user_id'] = $user->id;
        }

        LogAcess::query()->create($data);
        return $next($request);
    }
}
