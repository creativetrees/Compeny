<?php

namespace App\Providers;

use App\Models\SiteSetting;
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
        // Make the singleton site settings available to every view & component.
        View::composer('*', function ($view) {
            $view->with('settings', SiteSetting::current());
        });
    }
}
