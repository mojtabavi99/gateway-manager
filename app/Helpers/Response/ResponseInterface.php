<?php

namespace App\Helpers\Response;

interface ResponseInterface
{
    public function success(string $message, array $data = [], array $meta = [], int $code = 200): mixed;
    public function error(string $message, array $data = [], array $meta = [], int $code = 400): mixed;
}
