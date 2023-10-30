<?php

namespace App\Http\Middleware;

use App\Models\LogAcess;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        LogAcess::query()->create([
            'ip' => $ip,
            'route' => $rota
        ]);

        return $next($request);
    }
}
