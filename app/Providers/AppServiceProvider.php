<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Course;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentManager;
use App\Services\SettingsRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class);
        $this->app->singleton(SettingsRepository::class);

        $this->app->bind(
            PaymentGateway::class,
            fn ($app): PaymentGateway => $app->make(PaymentManager::class)->driver(),
        );
    }

    public function boot(): void
    {
        // Surface accidental N+1 queries during development instead of shipping them.
        Model::preventLazyLoading(! $this->app->isProduction());

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        $this->registerRateLimiters();
        $this->shareViewData();
    }

    /**
     * Throttles for the authentication endpoints.
     *
     * Keys combine the submitted email with the client address so one attacker
     * cannot lock a victim out by hammering their address alone.
     */
    private function registerRateLimiters(): void
    {
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)
            ->by(Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip())));

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(3)
            ->by(Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip())));

        RateLimiter::for('contact', fn (Request $request) => Limit::perMinutes(10, 3)->by($request->ip()));

        RateLimiter::for('progress', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
    }

    /**
     * Data the chrome needs on every render, resolved once per view rather than
     * queried from inside a Blade template.
     */
    private function shareViewData(): void
    {
        View::composer(
            ['components.layouts.public', 'components.layouts.app', 'components.layouts.admin', 'components.layouts.learn'],
            function ($view): void {
                $view->with('academySettings', $this->app->make(SettingsRepository::class)->all());
            },
        );

        View::composer('components.site.footer', function ($view): void {
            $view->with('footerCourses', Course::query()->published()->ordered()->take(5)->get(['id', 'title', 'slug']));
        });

        View::composer('components.layouts.admin', function ($view): void {
            $view->with('unreadMessages', ContactMessage::query()->where('is_read', false)->count());
        });
    }
}
