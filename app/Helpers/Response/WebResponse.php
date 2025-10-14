<?php

namespace App\Helpers\Response;

use Illuminate\Support\Str;

class WebResponse implements ResponseInterface
{
    /**
     * Return a success response as array
     */
    public function success(string $message, array $data = [], array $meta = [], int $code = 200): array
    {
        $defaultMeta = [
            'env' => app()->environment(),
            'request_id' => (string) Str::uuid(),
            'timestamp' => now()->toISOString(),
        ];

        return [
            'success' => true,
            'status_code' => $code,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge($defaultMeta, $meta),
        ];
    }

    /**
     * Return an error response as array
     */
    public function error(string $message, array $data = [], array $meta = [], int $code = 400): array
    {
        $defaultMeta = [
            'env' => app()->environment(),
            'request_id' => (string) Str::uuid(),
            'timestamp' => now()->toISOString(),
        ];

        return [
            'success' => false,
            'status_code' => $code,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge($defaultMeta, $meta),
        ];
    }
}
