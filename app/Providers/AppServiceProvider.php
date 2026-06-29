<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Global content() helper for editable site copy (Site Content resource).
        require_once __DIR__.'/../Support/helpers.php';
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fail fast in production if the app key was never generated — surface a
        // misconfigured deploy at boot rather than as a later encryption error.
        if ($this->app->environment('production') && ! $this->app->runningInConsole() && empty(config('app.key'))) {
            throw new \RuntimeException('APP_KEY is not set — run `php artisan key:generate`.');
        }

        // App-level HTTPS backstop in production (defense-in-depth alongside the
        // proxy X-Forwarded-Proto and the nginx http→https redirect).
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            // Never let auth codes (MFA email-OTP, password resets) be written to
            // log files — the log/array mailers would expose them in plaintext.
            if (in_array(config('mail.default'), ['log', 'array'], true)) {
                logger()->warning('MAIL_MAILER is "'.config('mail.default').'" in production — auth OTP / reset emails will be logged, not delivered. Configure a real SMTP transport.');
            }
        }

        // Filament Shield super-admin: the `super_admin` role bypasses every gate
        // and policy. Registered globally here (rather than via Shield's per-panel
        // interceptor, which did not fire) so it also applies in tests & console.
        Gate::before(fn ($user, string $ability): ?bool => ($user instanceof User && $user->hasRole('super_admin')) ? true : null);

        // Make the singleton site settings available to every view & component.
        View::composer('*', function ($view) {
            $view->with('settings', SiteSetting::current());
        });
    }
}
