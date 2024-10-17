<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/status',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'user_main_sub' => \App\Http\Middleware\UserMainSubPermission::class,
            'user_ms_sub' => \App\Http\Middleware\UserMsSubPermission::class,
            'user_avito_sub' => \App\Http\Middleware\UserAvitoSubPermission::class,
            'user_ozon_sub' => \App\Http\Middleware\UserOzonSubPermission::class,
            'user_wb_sub' => \App\Http\Middleware\UserWbSubPermission::class,
            'dynamic.auth' => \App\Http\Middleware\DynamicAuthGuard::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
