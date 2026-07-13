<?php

namespace Tests\Feature;

use App\Filament\Resources\PricingIncludes\Pages\CreatePricingInclude;
use App\Filament\Resources\PricingTiers\Pages\CreatePricingTier;
use App\Filament\Resources\Principles\Pages\CreatePrinciple;
use App\Filament\Resources\ProcessPhases\Pages\CreateProcessPhase;
use App\Filament\Resources\StartSteps\Pages\CreateStartStep;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RequiredFieldValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_pricing_tier_tagline_is_validated_not_crashed_when_blank(): void
    {
        Livewire::test(CreatePricingTier::class)
            ->fillForm(['name' => 'Sprint', 'term' => '2 weeks', 'price' => '$1', 'tagline' => ''])
            ->call('create')
            ->assertHasFormErrors(['tagline']);

        $this->assertDatabaseCount('pricing_tiers', 0);
    }

    public function test_process_phase_body_is_validated_not_crashed_when_blank(): void
    {
        Livewire::test(CreateProcessPhase::class)
            ->fillForm(['name' => 'Discover', 'summary' => 'x', 'body' => ''])
            ->call('create')
            ->assertHasFormErrors(['body']);

        $this->assertDatabaseCount('process_phases', 0);
    }

    public function test_principle_description_is_validated_not_crashed_when_blank(): void
    {
        Livewire::test(CreatePrinciple::class)
            ->fillForm(['name' => 'Ship to learn', 'description' => ''])
            ->call('create')
            ->assertHasFormErrors(['description']);

        $this->assertDatabaseCount('principles', 0);
    }

    public function test_start_step_description_is_validated_not_crashed_when_blank(): void
    {
        Livewire::test(CreateStartStep::class)
            ->fillForm(['name' => 'Send a brief', 'description' => ''])
            ->call('create')
            ->assertHasFormErrors(['description']);

        $this->assertDatabaseCount('start_steps', 0);
    }

    public function test_pricing_include_description_can_be_saved_blank_without_crashing(): void
    {
        // This field's own helper text says "Optional detail explaining the
        // inclusion" — so blank must save successfully (nullable column),
        // not require a value.
        Livewire::test(CreatePricingInclude::class)
            ->fillForm(['label' => 'Dedicated senior team', 'description' => ''])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseCount('pricing_includes', 1);
    }
}
