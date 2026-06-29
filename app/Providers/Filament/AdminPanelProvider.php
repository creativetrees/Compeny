<?php

namespace App\Providers\Filament;

use App\Filament\Auth\ForgotPassword;
use App\Filament\Auth\Login;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\PricingIncludes\PricingIncludeResource;
use App\Filament\Resources\PricingTiers\PricingTierResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
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
            // 2FA = authenticator app (TOTP) + recovery codes — the native
            // Filament v5 factor, fully offline. Email OTP was dropped because the
            // host's mail relay rejects transactional mail as spam (rSPAM bounce).
            // Enrol it from the Profile page; once enrolled, set
            // PANEL_MFA_REQUIRED=true to enforce (recovery codes prevent lock-out).
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->brandName('Creative Trees Group'),
            ], isRequired: (bool) config('panel.mfa_required'))
            ->brandName('Creative Trees Group')
            ->colors([
                'primary' => Color::Zinc,
            ])
            // Segmented OTP input (2FA setup + login challenge). The digits are
            // painted by a single overlaid <input> whose letter-spacing is
            // calibrated against Filament's 2rem box width — so we DON'T resize the
            // boxes (that desyncs the digits). We only centre the whole group as one
            // unit (fit-content + auto margins keeps the overlay aligned) and tint
            // the filled boxes with the brand orange instead of the zinc primary.
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => '<style>'
                    .'.fi-one-time-code-input-ctn{width:-moz-fit-content;width:fit-content;margin-inline:auto;}'
                    .'.fi-one-time-code-input-ctn>.fi-one-time-code-input-digit-field.fi-active{border-color:#f97316;}'
                    .'</style>',
            )
            ->navigationGroups([
                'Work',
                'Catalog',
                'Content',
                'Inbox',
                'Settings',
            ])
            // Synthetic sidebar parents: group related Content resources into
            // expandable sub-menus. The child resources point back here via their
            // navigationParentItem; the parent is hidden when the user can view
            // none of its children, and otherwise links to the primary child.
            ->navigationItems([
                NavigationItem::make('Pricing')
                    ->group('Content')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->sort(10)
                    ->visible(fn (): bool => PricingTierResource::canViewAny() || PricingIncludeResource::canViewAny())
                    ->url(fn (): string => PricingTierResource::getUrl()),
                NavigationItem::make('Showcase')
                    ->group('Content')
                    ->icon(Heroicon::OutlinedStar)
                    ->sort(3)
                    ->visible(fn (): bool => ClientResource::canViewAny() || TestimonialResource::canViewAny())
                    ->url(fn (): string => ClientResource::getUrl()),
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
