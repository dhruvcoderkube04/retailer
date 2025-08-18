<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log if needed
        });
    }

    /**
     * Handle unauthenticated exceptions (custom for API).
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return ApiResponse::error('Unauthenticated');
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Global API exception rendering.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {

            if ($exception instanceof ValidationException) {
                return ApiResponse::error('Validation failed', $exception->errors());
            }

            if ($exception instanceof NotFoundHttpException) {
                return ApiResponse::error('Route not found');
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return ApiResponse::error('HTTP method not allowed');
            }

            if ($exception instanceof AuthenticationException) {
                return ApiResponse::error('Unauthenticated');
            }

            // Fallback for other exceptions
            $message = config('app.debug') ? $exception->getMessage() : 'Something went wrong';

            return ApiResponse::error($message);
        }

        return parent::render($request, $exception);
    }
}
