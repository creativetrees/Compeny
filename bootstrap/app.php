<?php

use App\Http\Middleware\SecurityHeaders;
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
        // The app runs behind a TLS-terminating proxy (ngrok in dev, nginx/cPanel
        // in production). Trust forwarded headers so generated URLs (asset()/@vite,
        // canonical, redirects) use the correct https scheme. Default to loopback +
        // private ranges (covers ngrok-on-localhost, cPanel, Docker networks) rather
        // than '*', so a spoofed X-Forwarded-For from the public internet cannot
        // defeat IP-based rate limiting. Set TRUSTED_PROXIES=* only behind a CDN
        // (e.g. Cloudflare) whose edge IPs are public.
        $trustedProxies = (string) env('TRUSTED_PROXIES', '127.0.0.1,::1,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16');
        $middleware->trustProxies(
            at: $trustedProxies === '*' ? '*' : array_map('trim', explode(',', $trustedProxies)),
        );

        // Baseline security headers on every web response (public site + admin).
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
