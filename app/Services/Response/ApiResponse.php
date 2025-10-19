<?php

namespace App\Services\Response;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ApiResponse implements ResponseInterface
{
    public function success(string $message, array $data = [], array $meta = [], int $code = 200): JsonResponse
    {
        $defaultMeta = [
            'env' => app()->environment(),
            'request_id' => (string)Str::uuid(),
            'timestamp' => now()->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'status_code' => $code,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge($defaultMeta, $meta),
        ]);
    }

    public function error(string $message, array $data = [], array $meta = [], int $code = 400): JsonResponse
    {
        $defaultMeta = [
            'env' => app()->environment(),
            'request_id' => (string)Str::uuid(),
            'timestamp' => now()->toISOString(),
        ];

        return response()->json([
            'success' => false,
            'status_code' => $code,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge($defaultMeta, $meta),
        ]);
    }
}
