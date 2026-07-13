<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Models\Category;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SlugUniquenessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_duplicate_category_slug_is_validated_not_a_raw_db_error(): void
    {
        Category::factory()->create(['slug' => 'design']);

        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => 'Design Two', 'slug' => 'design'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_duplicate_product_slug_is_validated_not_a_raw_db_error(): void
    {
        Product::factory()->create(['slug' => 'starter-kit']);

        Livewire::test(CreateProduct::class)
            ->fillForm(['name' => 'Starter Kit Two', 'slug' => 'starter-kit'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertDatabaseCount('products', 1);
    }

    public function test_duplicate_project_slug_is_validated_not_a_raw_db_error(): void
    {
        Project::factory()->create(['slug' => 'my-project']);

        Livewire::test(CreateProject::class)
            ->fillForm(['title' => 'My Project Two', 'slug' => 'my-project'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertDatabaseCount('projects', 1);
    }

    public function test_duplicate_service_slug_is_validated_not_a_raw_db_error(): void
    {
        Service::factory()->create(['slug' => 'design-systems']);

        Livewire::test(CreateService::class)
            ->fillForm(['title' => 'Design Systems Two', 'slug' => 'design-systems'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertDatabaseCount('services', 1);
    }

    public function test_duplicate_team_member_slug_is_validated_not_a_raw_db_error(): void
    {
        TeamMember::factory()->create(['slug' => 'jane-doe']);

        Livewire::test(CreateTeamMember::class)
            ->fillForm(['name' => 'Jane Doe Two', 'slug' => 'jane-doe', 'role' => 'Designer'])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        $this->assertDatabaseCount('team_members', 1);
    }
}
