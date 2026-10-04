<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\EnsureDemoAccountIsReadOnly;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsTrainer;
use App\Http\Middleware\RecordLastLogin;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // Deliberately outside the `web` group: gateway webhooks carry no
            // session or CSRF token, so this is excluded by construction
            // rather than by remembering a CSRF exemption list.
            Route::prefix('webhooks')->name('webhooks.')->group(__DIR__.'/../routes/webhooks.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'trainer' => EnsureUserIsTrainer::class,
        ]);

        // The container publishes no host ports, so the only thing that can
        // reach it is the edge-proxy on the shared network. Without this the
        // app sees plain HTTP and generates http:// URLs behind HTTPS.
        $middleware->trustProxies(at: '*');

        // Global, not just `web`: webhook and health endpoints get them too.
        $middleware->append(AddSecurityHeaders::class);

        $middleware->appendToGroup('web', RecordLastLogin::class);
        $middleware->appendToGroup('web', EnsureDemoAccountIsReadOnly::class);

        $middleware->redirectTo(
            guests: fn (Request $request): string => route('login'),
            // Staff never land on the student dashboard after signing in.
            users: fn (Request $request): string => match (true) {
                $request->user() instanceof User && $request->user()->isAdmin() => route('admin.dashboard'),
                $request->user() instanceof User && $request->user()->isTrainer() => route('tutor.dashboard'),
                default => route('dashboard'),
            },
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
