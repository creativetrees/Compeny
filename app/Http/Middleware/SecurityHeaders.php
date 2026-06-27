<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline hardening headers applied to every web response.
 *
 * Conservative on purpose: no Content-Security-Policy (the admin panel and
 * Alpine rely on inline styles, and a wrong CSP would break the site). These
 * headers are safe defaults that don't change rendering.
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
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
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
