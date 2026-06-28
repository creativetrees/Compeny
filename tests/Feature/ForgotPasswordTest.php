<?php

namespace Tests\Feature;

use App\Filament\Auth\ForgotPassword;
use App\Models\User;
use App\Support\PasswordResetOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $attrs = []): User
    {
        return User::factory()->admin()->create(array_merge([
            'email' => 'admin@creativetrees.group',
            'username' => 'ctgadmin',
            'nik' => null,
            'password' => 'old-password',
        ], $attrs));
    }

    public function test_the_wizard_page_renders(): void
    {
        $this->get('/admin/password-reset/request')->assertOk();
    }

    // ── PasswordResetOtp: the server-authoritative security core ──────────────

    public function test_issue_then_verify_succeeds_without_nik(): void
    {
        $admin = $this->admin();

        $code = PasswordResetOtp::issue($admin);

        $this->assertNotNull($code);
        $this->assertSame(6, strlen($code));
        $this->assertTrue(PasswordResetOtp::verify($code, null));
        $this->assertTrue($admin->is($admin) && PasswordResetOtp::verifiedUser()?->is($admin));
    }

    public function test_verify_fails_with_a_wrong_code(): void
    {
        $admin = $this->admin();
        PasswordResetOtp::issue($admin);

        $this->assertFalse(PasswordResetOtp::verify('000000', null));
        $this->assertNull(PasswordResetOtp::verifiedUser());
    }

    public function test_nik_is_required_when_the_account_has_one(): void
    {
        $admin = $this->admin(['nik' => '1234567890123456']);
        $code = PasswordResetOtp::issue($admin);

        $this->assertTrue(PasswordResetOtp::requiresNik());
        $this->assertFalse(PasswordResetOtp::verify($code, '9999999999999999')); // wrong NIK
        $this->assertTrue(PasswordResetOtp::verify($code, '1234567890123456'));  // correct
    }

    public function test_code_is_burned_after_too_many_attempts(): void
    {
        $admin = $this->admin();
        $code = PasswordResetOtp::issue($admin);

        for ($i = 0; $i < 5; $i++) {
            $this->assertFalse(PasswordResetOtp::verify('111111', null));
        }

        // Even the correct code now fails — the code was burned.
        $this->assertFalse(PasswordResetOtp::verify($code, null));
    }

    public function test_verify_fails_when_no_code_was_issued(): void
    {
        $this->assertFalse(PasswordResetOtp::verify('123456', null));
    }

    // ── The final reset step (request) ────────────────────────────────────────

    public function test_request_resets_password_for_a_verified_session(): void
    {
        $admin = $this->admin();

        // The component mounts first (which clears any stale state); we then
        // simulate a completed wizard by issuing + verifying a code.
        $component = Livewire::test(ForgotPassword::class);

        $code = PasswordResetOtp::issue($admin);
        $this->assertTrue(PasswordResetOtp::verify($code, null));

        $component->set('data.password', 'NewSecret123!')->call('request');

        $this->assertTrue(Hash::check('NewSecret123!', $admin->fresh()->password));
        $this->assertNull(PasswordResetOtp::verifiedUser()); // state cleared after reset
    }

    public function test_request_is_rejected_without_a_verified_session(): void
    {
        $admin = $this->admin();

        Livewire::test(ForgotPassword::class)
            ->set('data.password', 'NewSecret123!')
            ->call('request')
            ->assertHasErrors(['data.password']);

        $this->assertTrue(Hash::check('old-password', $admin->fresh()->password));
    }
}
