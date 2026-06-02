<?php

use App\Http\Middleware\EnsureAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Unauthenticated → redirect to admin login (prevents redirect loop)
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Authenticated (but accessing guest-only routes) → redirect to dashboard
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));

        // Register EnsureAdmin as an alias
        $middleware->alias([
            'ensure.admin' => EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

