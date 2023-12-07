<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class apiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (env('APP_RUN') != 'development') {
            try {
                JWTAuth::parseToken()->authenticate();
            } catch (TokenInvalidException) {
                return response()->json(['status' => 'Token is invalid'], 401);
            } catch (TokenExpiredException) {
                try {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    JWTAuth::setToken($refreshed)->toUser();
                    $request->headers->set('Authorization', 'Bearer ' . $refreshed);
                } catch (JWTException) {
                    return response()->json([
                        'status' => 'Token cannot be refreshed, please login again'
                    ], 401);
                }
            } catch (Exception $e) {
                return response()->json(['status' => 'Authorization token not found'], '401');
            }
        }
        return $next($request);
    }
}
