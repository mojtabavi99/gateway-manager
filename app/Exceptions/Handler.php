<?php

namespace App\Exceptions;

use App\Helpers\Response\ResponseInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler
 *
 * Central exception handler for API and Web requests.
 *
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    protected ?ResponseInterface $response = null;
    protected $dontReport = [
        // Add exceptions you don't want to report, e.g., BaseException if not critical
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        // Central logging for all exceptions
        // TODO: use Event-Driven system for logging and monitoring

        parent::report($e);
    }

    /**
     * Render exception as HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response|RedirectResponse
     */
    public function render($request, Throwable $e): JsonResponse|Response|RedirectResponse
    {
        $this->response ??= app(ResponseInterface::class);

        if ($this->isJsonRequest($request)) {
            return $this->handleApiException($e);
        }

        return $this->handleWebException($e);
    }

    /**
     * Determine if request expects JSON.
     */
    protected function isJsonRequest(Request $request): bool
    {
        return $request->expectsJson() || $request->is('api/*');
    }

    /**
     * Handle API exceptions.
     */

    protected function handleApiException(Throwable $e): JsonResponse
    {
        $debug = config('app.debug');
        $meta = $debug ? ['trace' => $e->getTraceAsString()] : [];

        return match (true) {
            $e instanceof BaseException => $this->response->error(
                $e->getMessage(),
                $e->getData(),
                [],
                $e->getStatusCode()
            ),
            $e instanceof ValidationException => $this->response->error(
                __('generic::alerts.error.validation_failed'),
                ['errors' => $e->errors()],
                [],
                SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY
            ),
            $e instanceof AuthenticationException => $this->response->error(
                __('generic::alerts.error.unauthenticated'),
                [],
                $meta,
                SymfonyResponse::HTTP_UNAUTHORIZED
            ),
            $e instanceof AuthorizationException => $this->response->error(
                __('generic::alerts.error.forbidden'),
                [],
                $meta,
                SymfonyResponse::HTTP_FORBIDDEN
            ),
            $e instanceof ModelNotFoundException => $this->response->error(
                __('generic::alerts.error.record_not_found'),
                [],
                $meta,
                SymfonyResponse::HTTP_NOT_FOUND
            ),
            $e instanceof NotFoundHttpException => $this->response->error(
                __('generic::alerts.error.page_not_found'),
                [],
                $meta,
                SymfonyResponse::HTTP_NOT_FOUND
            ),
            $e instanceof MethodNotAllowedHttpException => $this->response->error(
                __('generic::alerts.error.method_not_allowed'),
                [],
                $meta,
                SymfonyResponse::HTTP_METHOD_NOT_ALLOWED
            ),
            $e instanceof QueryException => $this->response->error(
                $debug ? $e->getMessage() : __('generic::alerts.error.internal_server_error'),
                [],
                $meta,
                SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR
            ),
            $e instanceof ThrottleRequestsException => $this->response->error(
                __('generic::alerts.error.too_many_requests'),
                ['retry_after' => $e->getHeaders()['Retry-After'] ?? 60],
                $meta,
                SymfonyResponse::HTTP_TOO_MANY_REQUESTS
            ),
            $e instanceof HttpException => $this->response->error(
                $e->getMessage() ?: __('generic::alerts.error.http_request_error'),
                [],
                $meta,
                $e->getStatusCode()
            ),
            default => $this->response->error(
                $debug ? $e->getMessage() : __('generic::alerts.error.internal_server_error'),
                [],
                $meta,
                method_exists($e, 'getStatusCode') ? $e->getStatusCode() : SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR
            ),
        };
    }

    /**
     * Handle Web exceptions.
     */
    protected function handleWebException(Throwable $e): Response|RedirectResponse
    {
        dd($e);
        if ($e instanceof BaseException) {
            return response()->view('errors.generic', [
                'title' => 'ERROR!',
                'message' => $e->getMessage(),
                'status' => $e->getStatusCode(),
                'data' => $e->getData(),
            ], $e->getStatusCode());
        }

        if ($e instanceof ValidationException) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;

        if (view()->exists("errors.$status")) {
            return response()->view("errors.$status", ['message' => $e->getMessage()], $status);
        }

        return response()->view('errors.generic', ['message' => $e->getMessage(), 'status' => $status], $status);
    }
}
