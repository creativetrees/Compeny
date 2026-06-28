<?php

namespace Tests\Feature;

use App\Filament\Auth\ForgotPassword;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function admin(array $attrs = []): User
    {
        return User::factory()->admin()->create(array_merge([
            'email' => 'admin@creativetrees.group',
            'username' => 'ctgadmin',
            'nik' => null,
            'password' => 'old-password',
        ], $attrs));
    }

    public function test_full_otp_reset_flow(): void
    {
        Mail::fake();
        $admin = $this->admin();

        $component = Livewire::test(ForgotPassword::class)
            ->set('data.email', 'admin@creativetrees.group')
            ->set('data.username', 'CTGAdmin') // case-insensitive
            ->call('request')
            ->assertHasNoErrors()
            ->assertSet('step', 2);

        $code = null;
        Mail::assertSent(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail) use (&$code) {
            $code = $mail->code;

            return $mail->hasTo('admin@creativetrees.group');
        });
        $this->assertNotNull($code);

        $component->set('data.otp', $code)
            ->call('request')
            ->assertHasNoErrors()
            ->assertSet('step', 3);

        $component->set('data.password', 'NewSecret123!')
            ->set('data.password_confirmation', 'NewSecret123!')
            ->call('request');

        $this->assertTrue(Hash::check('NewSecret123!', $admin->fresh()->password));
    }

    public function test_wrong_code_is_rejected_and_stays_on_step_two(): void
    {
        Mail::fake();
        $this->admin();

        $component = Livewire::test(ForgotPassword::class)
            ->set('data.email', 'admin@creativetrees.group')
            ->set('data.username', 'ctgadmin')
            ->call('request')
            ->assertSet('step', 2);

        $component->set('data.otp', '000000')
            ->call('request')
            ->assertHasErrors(['data.otp'])
            ->assertSet('step', 2);
    }

    public function test_unknown_account_is_non_enumerating_and_cannot_reset(): void
    {
        Mail::fake();

        $component = Livewire::test(ForgotPassword::class)
            ->set('data.email', 'nobody@example.com')
            ->set('data.username', 'nobody')
            ->call('request')
            ->assertHasNoErrors()
            ->assertSet('step', 2); // advances regardless — no account disclosure

        Mail::assertNothingSent();

        $component->set('data.otp', '123456')
            ->call('request')
            ->assertHasErrors(['data.otp'])
            ->assertSet('step', 2);
    }
}
