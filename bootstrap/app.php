<?php

use App\Http\Middleware\EnsureHasRole;
use App\Http\Middleware\EnsureNotSuspended;
use App\Http\Middleware\EnsureUserIsOnboarded;
use App\Http\Middleware\UpdateLastSeenAt;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'onboarded' => EnsureUserIsOnboarded::class,
            'role' => EnsureHasRole::class,
            'not_suspended' => EnsureNotSuspended::class,
        ]);

        $middleware->throttleApi();

        $middleware->web(append: [
            UpdateLastSeenAt::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\RuntimeException $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        });
    })->create();
