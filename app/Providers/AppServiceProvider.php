<?php

namespace App\Providers;

use App\Enums\SupportTicketStatus;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\CourseQuestion;
use App\Models\SupportTicket;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentManager;
use App\Services\Payments\PhonePeGateway;
use App\Services\Payments\RazorpayGateway;
use App\Services\SettingsRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function ($app): PaymentManager {
            $manager = new PaymentManager($app);
            $manager->extend('razorpay', RazorpayGateway::class);
            $manager->extend('phonepe', PhonePeGateway::class);

            return $manager;
        });

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
        $this->brandAuthMailNotifications();
    }

    /**
     * Laravel's built-in password-reset and email-verification notifications
     * otherwise render its generic default mail theme — replace both with
     * the same branded template every other academy email uses.
     */
    private function brandAuthMailNotifications(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset your '.config('academy.name').' password')
                ->view('emails.templated', [
                    'body' => 'A password reset was requested for your account. This link expires in '.config('auth.passwords.users.expire', 60).' minutes. If you did not request this, no action is needed.',
                    'url' => $url,
                    'buttonLabel' => 'Reset password',
                ]);
        });

        VerifyEmail::toMailUsing(function (MustVerifyEmail $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Confirm your email address')
                ->view('emails.templated', [
                    'body' => 'Please confirm your email address to finish setting up your account.',
                    'url' => $url,
                    'buttonLabel' => 'Verify email address',
                ]);
        });
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

        RateLimiter::for('register', fn (Request $request) => Limit::perMinutes(10, 5)->by($request->ip()));

        RateLimiter::for('contact', fn (Request $request) => Limit::perMinutes(10, 3)->by($request->ip()));

        RateLimiter::for('progress', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(6)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('webhook-razorpay', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        RateLimiter::for('webhook-phonepe', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
    }

    /**
     * Data the chrome needs on every render, resolved once per view rather than
     * queried from inside a Blade template.
     */
    private function shareViewData(): void
    {
        View::composer(
            [
                'components.layouts.public', 'components.layouts.app', 'components.layouts.admin',
                'components.layouts.tutor', 'components.layouts.learn', 'components.layouts.auth',
                'components.layouts.error', 'components.site.logo', 'components.site.brand-head',
                'components.site.certificate',
            ],
            function ($view): void {
                $repository = $this->app->make(SettingsRepository::class);
                $view->with('academySettings', [
                    ...$repository->all(),
                    'logo_url' => $repository->imageUrl('logo_path'),
                    'favicon_url' => $repository->imageUrl('favicon_path'),
                ]);
            },
        );

        View::composer('components.site.footer', function ($view): void {
            $view->with('footerCourses', Course::query()->published()->ordered()->take(5)->get(['id', 'title', 'slug']));
        });

        View::composer('components.layouts.admin', function ($view): void {
            $view->with('unreadMessages', ContactMessage::query()->where('is_read', false)->count());
            $view->with('openSupportTickets', SupportTicket::query()->whereNot('status', SupportTicketStatus::Resolved)->count());
            $view->with('unansweredQuestions', CourseQuestion::query()->doesntHave('replies')->count());
        });

        View::composer('components.layouts.app', function ($view): void {
            $user = auth()->user();
            $view->with('unreadNotifications', $user?->unreadNotifications()->count() ?? 0);
        });
    }
}
