<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Filament panel's security boundary is User::canAccessPanel(), which is
 * default-deny: only users flagged is_admin = true may enter. These tests prove
 * the denial path that the resource-render coverage in AdminPanelTest does not.
 */
class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_non_admin_is_denied(): void
    {
        $user = User::factory()->create(); // is_admin = false (default)

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_flagged_user_reaches_the_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertSuccessful();
    }

    /** Regression: is_admin must never be settable via mass-assignment. */
    public function test_is_admin_cannot_be_mass_assigned(): void
    {
        $user = User::create([
            'name' => 'Sneaky User',
            'username' => 'sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'secret123',
            'is_admin' => true, // attempt to self-escalate
        ]);

        $this->assertFalse((bool) $user->fresh()->is_admin);
    }
}
