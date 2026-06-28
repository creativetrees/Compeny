<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline hardening headers applied to every web response.
 *
 * CSP is layered: a small set of script-agnostic directives is ENFORCED on
 * every response (clickjacking / base-uri / plugin lockdown — these don't touch
 * script execution, so Alpine/GSAP keep working), while the full strict policy
 * ships as Report-Only on the public site. Enforcing the strict script policy
 * (no 'unsafe-eval') requires migrating Alpine to the @alpinejs/csp build.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), browsing-topics=()',
            // Safe to enforce everywhere: no effect on inline scripts/styles, real
            // clickjacking + base-tag + plugin protection. frame-ancestors
            // supersedes X-Frame-Options on modern browsers.
            'Content-Security-Policy' => "frame-ancestors 'self'; base-uri 'self'; object-src 'none'",
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Full strict policy in Report-Only on the public site (admin/Filament
        // needs its own policy). Report-Only does NOT block — it surfaces
        // violations so we can tighten toward enforcing 'self'-only scripts.
        if (! $request->is('admin', 'admin/*')) {
            $response->headers->set('Content-Security-Policy-Report-Only', implode('; ', [
                "default-src 'self'",
                "script-src 'self'",
                "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
                "font-src 'self' https://fonts.bunny.net",
                "img-src 'self' data: https:",
                "connect-src 'self' ws: wss:",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ]));
        }

        // HSTS only over real HTTPS so local http:// is never forced to https.
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Don't leak the framework version.
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
