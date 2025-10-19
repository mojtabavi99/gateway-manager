<?php

namespace App\Services;

use App\Services\Response\ResponseInterface;

abstract class Service
{
    protected ResponseInterface $response;

    public function __construct()
    {
        $this->response = app(ResponseInterface::class);;
    }
}
