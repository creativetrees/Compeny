<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_dashboard_and_every_resource_render(): void
    {
        // Panel access is gated on the is_admin flag (default-deny), so the test
        // admin must be flagged via the factory's admin() state.
        $admin = User::factory()->admin()->create(['email' => 'admin@creativetrees.group']);

        $this->actingAs($admin)->get('/admin')->assertSuccessful();

        $resources = [
            'categories', 'services', 'clients', 'team-members',
            'products', 'projects', 'testimonials', 'leads', 'site-settings',
            'pricing-tiers', 'pricing-includes', 'process-phases', 'principles',
            'faqs', 'start-steps', 'nav-links', 'site-contents',
        ];

        foreach ($resources as $resource) {
            $this->actingAs($admin)
                ->get("/admin/{$resource}")
                ->assertSuccessful();
        }
    }
}
