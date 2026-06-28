<?php

namespace Tests\Feature;

use App\Filament\Auth\Login;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Covers the user profile identity fields (username, NIK, phone) and the
 * username-or-email panel login built on top of them.
 */
class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('panel.allowed_email_domains', ['creativetrees.group']);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function admin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'CTG Admin',
            'username' => 'ctgadmin',
            'email' => 'admin@creativetrees.group',
            'nik' => '1234567890123456',
            'phone' => '081234567890',
            'password' => 'secret123',
        ], $attributes));
    }

    public function test_users_table_has_the_profile_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('users', ['username', 'nik', 'phone']));
    }

    public function test_resolve_login_field_distinguishes_email_from_username(): void
    {
        $this->assertSame(['email', 'a@b.com'], User::resolveLoginField('a@b.com'));
        $this->assertSame(['username', 'ctgadmin'], User::resolveLoginField('CTGAdmin'));
    }

    public function test_admin_can_authenticate_with_username(): void
    {
        $this->admin();

        Livewire::test(Login::class)
            ->set('data.login', 'ctgadmin')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticated();
    }

    public function test_admin_can_authenticate_with_email(): void
    {
        $this->admin();

        Livewire::test(Login::class)
            ->set('data.login', 'admin@creativetrees.group')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticated();
    }

    public function test_username_login_is_case_insensitive(): void
    {
        $this->admin();

        Livewire::test(Login::class)
            ->set('data.login', 'CTGADMIN')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticated();
    }

    public function test_authentication_fails_with_a_wrong_password(): void
    {
        $this->admin();

        Livewire::test(Login::class)
            ->set('data.login', 'ctgadmin')
            ->set('data.password', 'wrong-password')
            ->call('authenticate')
            ->assertHasFormErrors(['login']);

        $this->assertGuest();
    }

    public function test_username_nik_and_phone_are_unique_at_the_database_level(): void
    {
        $this->admin();

        foreach (
            [
                ['username' => 'ctgadmin', 'email' => 'a@creativetrees.group', 'nik' => '9999999999999999', 'phone' => '081200000001'],
                ['username' => 'other1', 'email' => 'b@creativetrees.group', 'nik' => '1234567890123456', 'phone' => '081200000002'],
                ['username' => 'other2', 'email' => 'c@creativetrees.group', 'nik' => '8888888888888888', 'phone' => '081234567890'],
            ] as $duplicate
        ) {
            try {
                User::factory()->create($duplicate);
                $this->fail('Expected a unique-constraint violation for: '.json_encode($duplicate));
            } catch (QueryException $e) {
                $this->assertTrue(true);
            }
        }
    }

    public function test_user_resource_index_renders_for_an_admin(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/users')->assertSuccessful();
    }
}
