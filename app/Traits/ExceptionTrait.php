<?php

namespace App\Traits;

use App\Exceptions\BaseException;

/**
 * Trait ExceptionTrait
 *
 * Provides helper methods for throwing standardized exceptions
 * in service-layer classes.
 *
 * @package App\Traits
 */
trait ExceptionTrait
{
    /**
     * Throw a validation exception (HTTP 422).
     *
     * @param string $message Exception message.
     * @param array $data Additional contextual data.
     *
     * @throws BaseException
     */
    protected function throwValidation(string $message = 'Validation failed.', array $data = []): void
    {
        // TODO: add logger
        throw new BaseException($message, 422, $data);
    }

    /**
     * Throw a not found exception (HTTP 404).
     *
     * @param string $message Exception message.
     * @param array $data Additional contextual data.
     *
     * @throws BaseException
     */
    protected function throwNotFound(string $message = 'Resource not found.', array $data = []): void
    {
        // TODO: add logger
        throw new BaseException($message, 404, $data);
    }

    /**
     * Throw an unauthorized exception (HTTP 401).
     *
     * @param string $message Exception message.
     * @param array $data Additional contextual data.
     *
     * @throws BaseException
     */
    protected function throwUnauthorized(string $message = 'Unauthorized.', array $data = []): void
    {
        // TODO: add logger
        throw new BaseException($message, 401, $data);
    }

    /**
     * Throw a forbidden exception (HTTP 403).
     *
     * @param string $message Exception message.
     * @param array $data Additional contextual data.
     *
     * @throws BaseException
     */
    protected function throwForbidden(string $message = 'Forbidden access.', array $data = []): void
    {
        // TODO: add logger
        throw new BaseException($message, 403, $data);
    }

    /**
     * Throw a server error exception (HTTP 500).
     *
     * @param string $message Exception message.
     * @param array $data Additional contextual data.
     *
     * @throws BaseException
     */
    protected function throwServerError(string $message = 'Internal server error.', array $data = []): void
    {
        // TODO: add logger
        throw new BaseException($message, 500, $data);
    }

    /**
     * Throw a custom exception with arbitrary HTTP status code.
     *
     * @param string $message Exception message.
     * @param int $statusCode HTTP status code.
     * @param array $data Additional contextual data.
     *
     * @throws BaseException
     */
    protected function throwCustom(string $message, int $statusCode = 400, array $data = []): void
    {
        // TODO: add logger
        throw new BaseException($message, $statusCode, $data);
    }
}
