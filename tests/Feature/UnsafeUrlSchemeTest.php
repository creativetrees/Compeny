<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\PricingTier;
use App\Models\Product;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnsafeUrlSchemeTest extends TestCase
{
    use RefreshDatabase;

    private const PAYLOAD = 'javascript:alert(document.cookie)';

    public function test_hero_and_footer_cta_urls_reject_unsafe_schemes(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'hero_cta_url' => self::PAYLOAD,
            'hero_cta_secondary_url' => self::PAYLOAD,
            'footer_cta_url' => self::PAYLOAD,
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('href="'.self::PAYLOAD, false);
    }

    public function test_pricing_tier_cta_url_rejects_unsafe_scheme(): void
    {
        PricingTier::create([
            'name' => 'Test Tier', 'term' => '1 month', 'price' => '$1', 'tagline' => 'x', 'items' => [],
        ]);
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'page_content' => ['pricing' => ['tier_cta_url' => self::PAYLOAD]],
        ]);

        $response = $this->get('/pricing');
        $response->assertOk();
        $response->assertDontSee('href="'.self::PAYLOAD, false);
    }

    public function test_header_nav_menu_item_url_rejects_unsafe_scheme(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'nav_menu' => [['label' => 'Evil', 'url' => self::PAYLOAD]],
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('href="'.self::PAYLOAD, false);
    }

    public function test_footer_and_contact_social_link_urls_reject_unsafe_scheme(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'social_links' => [['platform' => 'X', 'url' => self::PAYLOAD]],
        ]);

        $home = $this->get('/');
        $home->assertOk();
        $home->assertDontSee('href="'.self::PAYLOAD, false);

        $contact = $this->get('/contact');
        $contact->assertOk();
        $contact->assertDontSee('href="'.self::PAYLOAD, false);
    }

    public function test_client_marquee_website_url_rejects_unsafe_scheme(): void
    {
        Client::factory()->create(['website_url' => self::PAYLOAD]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('href="'.self::PAYLOAD, false);
    }

    public function test_project_website_url_rejects_unsafe_scheme(): void
    {
        $project = Project::factory()->create(['website_url' => self::PAYLOAD, 'status' => 'published']);

        $response = $this->get('/work/'.$project->slug);
        $response->assertOk();
        $response->assertDontSee('href="'.self::PAYLOAD, false);
    }

    public function test_product_cta_url_rejects_unsafe_scheme(): void
    {
        Product::factory()->create(['cta_url' => self::PAYLOAD, 'status' => 'published']);

        $response = $this->get('/products');
        $response->assertOk();
        $response->assertDontSee('href="'.self::PAYLOAD, false);
    }
}
