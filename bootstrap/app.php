<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/login.php',
            __DIR__ . '/../routes/profile.php',
            __DIR__ . '/../routes/campaign.php',
            __DIR__ . '/../routes/event.php',
            __DIR__ . '/../routes/inkind.php',
            __DIR__ . '/../routes/chatbot.php',
            __DIR__ . '/../routes/administrator.php',

        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',


    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
        $middleware->append(\App\Http\Middleware\AddSecurityHeaders::class);
        $middleware->web(append: ['throttle:public']);
        $middleware->alias([
            'admin.session' => \App\Http\Middleware\RequireAdminSession::class,
            'webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('login.page'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            $message = 'Too many requests. Please wait a moment before trying again.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 429, $e->getHeaders());
            }

            return back()->with('error', $message)->withHeaders($e->getHeaders());
        });

        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $limit = ini_get('post_max_size') ?: 'the server upload limit';
            $message = "The uploaded files are too large. Please keep the total upload under {$limit}.";

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 413);
            }

            return back()->withErrors(['files' => $message])->withInput();
        });
    })->create();
