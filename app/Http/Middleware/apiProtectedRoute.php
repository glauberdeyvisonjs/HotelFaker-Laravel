<?php /** @noinspection ALL */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            } catch (\Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                    return response()->json(['status' => 'Token is invalid'], 401);
                } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    try {
                        $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                        $user = JWTAuth::setToken($refreshed)->toUser();
                        $request->headers->set('Authorization', 'Bearer ' . $refreshed);
                    } catch (JWTException $e) {
                        return response()->json([
                            'code' => 103,
                            'message' => 'Token cannot be refreshed, please login again'
                        ], 103);
                    }
                } else {
                    return response()->json(['status' => 'Authorization token not found'], '401');
                }
            }
        }
        return $next($request);
    }
}
