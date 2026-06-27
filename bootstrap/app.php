<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The app always runs behind a TLS-terminating proxy (ngrok in dev,
        // nginx/cPanel in production). Trust the forwarded headers so generated
        // URLs (asset()/@vite, canonical, redirects) use the correct https
        // scheme — otherwise assets are emitted as http:// and blocked as
        // mixed content on an https page.
        $middleware->trustProxies(at: '*');

        // Baseline security headers on every web response (public site + admin).
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
