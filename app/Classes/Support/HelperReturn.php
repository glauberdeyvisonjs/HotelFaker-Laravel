<?php

namespace App\Classes\Support;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HelperReturn
{
    /**
     * @param Exception $exception
     * @return object
     */
    public static function returnException(Exception $exception): object
    {
        Log::error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'exception' => $exception->getTraceAsString(),
        ]);

        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['message' => 'Registro não encontrado'], 404);
        }

        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1062) {
                return response()->json(['message' => 'Registro duplicado'], 409);
            }
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json(['message' => 'Página não encontrada'], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json(['message' => 'Método não permitido'], 405);
        }

        if ($exception instanceof HttpException) {
            return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        $code = match ($exception->getCode()) {
            400 => 400,
            401 => 401,
            403 => 403,
            404 => 404,
            405 => 405,
            409 => 409,
            429 => 429,
            default => 500,
        };

        return response()->json([
            'status' => 'error',
            'message' => $exception->getMessage(),
            'exception' => $exception->getTraceAsString(),
        ], $code);
    }

    /**
     * @param string $key
     * @param $value
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function returnSuccess(string $key, $value, string $message = 'Requisição concluída com sucesso!', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            $key => $value,
        ], $code);
    }
}
