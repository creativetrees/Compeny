<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\NavLinks\NavLinkResource;
use App\Filament\Resources\PricingIncludes\PricingIncludeResource;
use App\Filament\Resources\PricingTiers\PricingTierResource;
use App\Filament\Resources\Principles\PrincipleResource;
use App\Filament\Resources\ProcessPhases\ProcessPhaseResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\StartSteps\StartStepResource;
use App\Filament\Resources\TeamMembers\TeamMemberResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\Category;
use App\Models\Client;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\NavLink;
use App\Models\PricingInclude;
use App\Models\PricingTier;
use App\Models\Principle;
use App\Models\ProcessPhase;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\StartStep;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminViewPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Smoke-test every resource View page (infolist). The previous suite only
     * covered list + edit pages, which is how the Project view 500 (a
     * {label,value} map rendered as a badge) slipped through. Renders the first
     * seeded record of each resource and asserts it does not 500.
     */
    public function test_every_resource_view_page_renders(): void
    {
        $this->seed();
        $admin = User::factory()->admin()->create(['email' => 'viewpages@creativetrees.group']);

        // [ResourceClass, ModelClass]
        $map = [
            [ProjectResource::class, Project::class],
            [ProductResource::class, Product::class],
            [ServiceResource::class, Service::class],
            [PricingTierResource::class, PricingTier::class],
            [PricingIncludeResource::class, PricingInclude::class],
            [ProcessPhaseResource::class, ProcessPhase::class],
            [TeamMemberResource::class, TeamMember::class],
            [TestimonialResource::class, Testimonial::class],
            [ClientResource::class, Client::class],
            [CategoryResource::class, Category::class],
            [PrincipleResource::class, Principle::class],
            [StartStepResource::class, StartStep::class],
            [NavLinkResource::class, NavLink::class],
            [FaqResource::class, Faq::class],
            [LeadResource::class, Lead::class],
        ];

        $checked = 0;

        foreach ($map as [$resource, $model]) {
            $record = $model::query()->first();

            if (! $record) {
                continue;
            }

            $this->actingAs($admin)
                ->get($resource::getUrl('view', ['record' => $record]))
                ->assertSuccessful();

            $checked++;
        }

        // Guard against a vacuous pass — most resources should be seeded.
        $this->assertGreaterThanOrEqual(8, $checked, "Only {$checked} view pages were exercised.");
    }
}
