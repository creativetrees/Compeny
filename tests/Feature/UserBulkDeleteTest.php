<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_bulk_delete_cannot_delete_the_currently_authenticated_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->callTableBulkAction('delete', [$admin->id, $other->id]);

        $this->assertModelExists($admin);
        $this->assertDatabaseMissing('users', ['id' => $other->id]);
    }
}
