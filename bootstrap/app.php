<?php

declare(strict_types=1);

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ModMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'mod' => ModMiddleware::class,
            'superAdmin' => AdminMiddleware::class,
        ]);
    })
    ->withCommands(glob(__DIR__ . '/../app/Modules/*/Console'))
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
