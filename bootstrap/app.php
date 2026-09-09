<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\RecordLastLogin;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);

        $middleware->appendToGroup('web', RecordLastLogin::class);

        $middleware->redirectTo(
            guests: fn (Request $request): string => route('login'),
            // Staff never land on the student dashboard after signing in.
            users: fn (Request $request): string => $request->user() instanceof User && $request->user()->isAdmin()
                ? route('admin.dashboard')
                : route('dashboard'),
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
