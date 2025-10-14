<?php

namespace App\Services;

use App\Helpers\Response\ResponseInterface;

abstract class Service
{
    protected ResponseInterface $response;

    public function __construct()
    {
        $this->response = app(ResponseInterface::class);;
    }
}
