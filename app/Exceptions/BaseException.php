<?php

namespace App\Exceptions;

use Exception;

/**
 * Class BaseException
 *
 * The base exception class for all custom service exceptions.
 * Stores an HTTP status code and additional contextual data.
 *
 * @package App\Exceptions
 */
class BaseException extends Exception
{
    /**
     * HTTP status code for the exception.
     *
     * @var int
     */
    protected int $statusCode = 500;

    /**
     * Additional data to include in the response.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Create a new BaseException instance.
     *
     * @param string $message Exception message.
     * @param int $statusCode HTTP status code (default 500).
     * @param array $data Additional contextual data.
     */
    public function __construct(string $message = '', int $statusCode = 500, array $data = [])
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get additional contextual data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
