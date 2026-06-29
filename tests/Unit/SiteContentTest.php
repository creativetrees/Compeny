<?php

namespace Tests\Unit;

use App\Models\SiteContent;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

 // app TestCase, not PHPUnit's — value() touches the DB

class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SiteContent::flushCache(); // reset the per-request static $store between cases
    }

    public function test_returns_default_when_key_is_missing(): void
    {
        $this->assertSame('Fallback copy', SiteContent::value('hero.title', 'Fallback copy'));
    }

    public function test_returns_default_when_stored_value_is_empty(): void
    {
        SiteContent::create(['group' => 'hero', 'key' => 'hero.title', 'label' => 'Hero title', 'value' => '', 'sort' => 0]);
        SiteContent::flushCache();

        $this->assertSame('Fallback copy', SiteContent::value('hero.title', 'Fallback copy'));
    }

    public function test_returns_stored_value_when_present(): void
    {
        SiteContent::create(['group' => 'hero', 'key' => 'hero.title', 'label' => 'Hero title', 'value' => 'Edited in CMS', 'sort' => 0]);
        SiteContent::flushCache();

        $this->assertSame('Edited in CMS', SiteContent::value('hero.title', 'Fallback copy'));
    }

    public function test_content_helper_reads_from_site_setting_page_content(): void
    {
        // The content() helper now resolves dotted keys from the Site Settings
        // page_content store (per-page copy), falling back to the given default.
        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            ['page_content' => ['hero' => ['cta' => 'Start now']]],
        );

        $this->assertSame('Start now', content('hero.cta', 'Start a project'));
        $this->assertSame('Default CTA', content('missing.key', 'Default CTA'));
    }
}
