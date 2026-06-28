<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Filament panel's security boundary is User::canAccessPanel() — an email
 * domain allow-list that fails closed. These tests prove the DENIAL paths, which
 * the happy-path-only coverage in AdminPanelTest does not.
 */
class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_with_disallowed_domain_is_denied(): void
    {
        config()->set('panel.allowed_email_domains', ['creativetrees.group']);

        $outsider = User::factory()->create(['email' => 'intruder@gmail.com']);

        $this->actingAs($outsider)->get('/admin')->assertForbidden();
    }

    public function test_domain_match_is_case_insensitive(): void
    {
        config()->set('panel.allowed_email_domains', ['creativetrees.group']);

        $admin = User::factory()->create(['email' => 'Admin@CreativeTrees.Group']);

        $this->actingAs($admin)->get('/admin')->assertSuccessful();
    }

    public function test_empty_allow_list_fails_closed_for_everyone(): void
    {
        config()->set('panel.allowed_email_domains', []);

        $user = User::factory()->create(['email' => 'admin@creativetrees.group']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
