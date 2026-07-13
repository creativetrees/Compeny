<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamSocialLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_javascript_url_in_a_social_link_is_not_rendered_as_an_href(): void
    {
        TeamMember::factory()->create([
            'socials' => ['linkedin' => 'javascript:alert(document.cookie)'],
        ]);

        $response = $this->get('/team');

        $response->assertOk();
        $response->assertDontSee('javascript:alert', false);
    }

    public function test_safe_social_links_still_render(): void
    {
        TeamMember::factory()->create([
            'socials' => ['linkedin' => 'https://linkedin.com/in/someone'],
        ]);

        $this->get('/team')->assertSee('https://linkedin.com/in/someone', false);
    }
}
