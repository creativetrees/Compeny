<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_analytics_script_carries_a_nonce_that_matches_the_csp_policy(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], ['google_analytics_id' => 'G-TESTID123']);

        $response = $this->get('/');
        $response->assertOk();

        // Either header may hold the strict policy depending on environment
        // (enforced in production, Report-Only elsewhere) — check whichever one
        // actually constrains script-src.
        $candidates = array_filter([
            $response->headers->get('Content-Security-Policy'),
            $response->headers->get('Content-Security-Policy-Report-Only'),
        ]);
        $csp = collect($candidates)->first(fn ($value) => str_contains($value, 'script-src'));

        $this->assertNotNull($csp, 'Expected a CSP header with a script-src directive on every response.');
        $this->assertMatchesRegularExpression('/script-src[^;]*\'nonce-[^\']+\'/', $csp);

        preg_match('/\'nonce-([^\']+)\'/', $csp, $m);
        $nonce = $m[1] ?? null;
        $this->assertNotNull($nonce);

        $response->assertSee('googletagmanager.com/gtag/js?id=G-TESTID123', false);
        $response->assertSee('nonce="'.$nonce.'"', false);
    }
}
