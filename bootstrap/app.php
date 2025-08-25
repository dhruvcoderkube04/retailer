<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RetailerMiddleware;
use App\Http\Middleware\ValidateApiKey;
use App\Http\Middleware\WholesalerMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'retailer' => RetailerMiddleware::class,
            'user.active' => \App\Http\Middleware\CheckRetailerStatus::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'lorrigo-webhook' // <-- exclude this route
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
