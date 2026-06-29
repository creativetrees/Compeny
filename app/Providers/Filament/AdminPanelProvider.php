<?php

namespace App\Providers\Filament;

use App\Filament\Auth\ForgotPassword;
use App\Filament\Auth\Login;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->passwordReset(ForgotPassword::class)
            // Two-step login MFA, REQUIRED. PRIMARY = email OTP: a 6-char
            // alphanumeric code (e.g. 3H9J4D) emailed to the admin and entered in
            // the segmented code input. This uses Filament's built-in email flow,
            // which hashes the code in the session, expires it (~4 min), makes it
            // single-use, and rate-limits both sending and verification.
            // FALLBACK = authenticator app (TOTP) + recovery codes, so the forced
            // enrolment never locks the admin out if email is temporarily down
            // (they can enrol the offline app factor instead).
            ->multiFactorAuthentication([
                EmailAuthentication::make(),
                AppAuthentication::make()
                    ->recoverable()
                    ->brandName('Creative Trees Group'),
            ], isRequired: (bool) config('panel.mfa_required'))
            ->brandName('Creative Trees Group')
            ->colors([
                'primary' => Color::Zinc,
            ])
            ->navigationGroups([
                'Work',
                'Catalog',
                'Content',
                'Inbox',
                'Settings',
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('20s')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
