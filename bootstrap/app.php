<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/login.php',
            __DIR__ . '/../routes/profile.php',
            __DIR__ . '/../routes/campaign.php',
            __DIR__ . '/../routes/event.php',
            __DIR__ . '/../routes/inkind.php',
            __DIR__ . '/../routes/administrator.php',

        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',


    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
