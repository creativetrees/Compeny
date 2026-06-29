<?php

namespace Tests\Feature;

use App\Filament\Resources\PricingTiers\PricingTierResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\SiteSettings\SiteSettingResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\PricingTier;
use App\Models\Product;
use App\Models\Project;
use App\Models\Testimonial;
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

    public function test_profile_page_exposes_two_factor_setup(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'profile@creativetrees.group']);

        $this->actingAs($admin)
            ->get('/admin/profile')
            ->assertSuccessful()
            ->assertSee('Authenticator', false);
    }

    public function test_tabbed_resource_edit_forms_render(): void
    {
        $this->seed();
        $admin = User::factory()->admin()->create(['email' => 'admin@creativetrees.group']);

        // Singleton settings resource opens straight into its (tabbed) edit form.
        $this->actingAs($admin)
            ->get(SiteSettingResource::getUrl('index'))
            ->assertSuccessful();

        // Render every tabbed edit form — fails if the Tabs/Section layout API is wrong.
        $forms = [
            [ProjectResource::class, Project::firstOrFail()],
            [ProductResource::class, Product::firstOrFail()],
            [TestimonialResource::class, Testimonial::firstOrFail()],
            [PricingTierResource::class, PricingTier::firstOrFail()],
            [UserResource::class, $admin],
        ];

        foreach ($forms as [$resource, $record]) {
            $this->actingAs($admin)
                ->get($resource::getUrl('edit', ['record' => $record]))
                ->assertSuccessful();
        }
    }
}
