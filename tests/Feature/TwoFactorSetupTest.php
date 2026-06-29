<?php

namespace Tests\Feature;

use App\Livewire\TwoFactorSetup;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorSetupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_mount_in_setup_view_primes_a_secret_and_renders_the_setup_key(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(TwoFactorSetup::class)
            ->assertOk()
            ->assertSet('view', 'setup')
            ->assertSee(session('two_factor_setup.secret')); // the manual setup key is shown

        $this->assertNotNull(session('two_factor_setup.secret'));
    }

    public function test_regenerate_mints_a_fresh_secret(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $component = Livewire::test(TwoFactorSetup::class);
        $first = session('two_factor_setup.secret');

        $component->call('regenerate');

        $this->assertNotSame($first, session('two_factor_setup.secret'));
        $this->assertNotNull(session('two_factor_setup.secret'));
    }

    public function test_verify_and_enable_with_a_valid_code_saves_the_secret(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = Livewire::test(TwoFactorSetup::class);
        $code = (new Google2FA)->getCurrentOtp(session('two_factor_setup.secret'));

        $component->set('data.otp', $code)
            ->call('verifyAndEnable')
            ->assertHasNoErrors();

        $component->assertSet('view', 'setup'); // stays for step 2 (codes)
        $this->assertNotNull($user->fresh()->app_authentication_secret);
        $this->assertNotEmpty(session('two_factor_setup.recoveryCodes'));
    }

    public function test_completing_the_wizard_switches_to_the_enabled_view(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = Livewire::test(TwoFactorSetup::class);
        $code = (new Google2FA)->getCurrentOtp(session('two_factor_setup.secret'));

        $component->set('data.otp', $code)->call('verifyAndEnable');
        $component->call('complete')->assertSet('view', 'enabled');

        $this->assertNull(session('two_factor_setup'));
        $this->assertNotNull($user->fresh()->app_authentication_secret);
    }

    public function test_verify_and_enable_with_an_invalid_code_is_rejected(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        Livewire::test(TwoFactorSetup::class)
            ->set('data.otp', '000000')
            ->call('verifyAndEnable')
            ->assertHasErrors('data.otp')
            ->assertSet('view', 'setup');

        $this->assertNull($user->fresh()->app_authentication_secret);
    }

    public function test_disable_turns_2fa_off_with_the_password(): void
    {
        $user = User::factory()->admin()->create(); // factory password is "password"
        $this->actingAs($user);

        $component = Livewire::test(TwoFactorSetup::class);
        $component->set('data.otp', (new Google2FA)->getCurrentOtp(session('two_factor_setup.secret')))
            ->call('verifyAndEnable');
        $component->call('complete')->assertSet('view', 'enabled');

        $component->set('disablePassword', 'password')->call('disable')->assertSet('view', 'setup');

        $this->assertNull($user->fresh()->app_authentication_secret);
        $this->assertNotNull(session('two_factor_setup.secret')); // a fresh pending secret is primed
    }

    public function test_disable_is_rejected_without_the_correct_password(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = Livewire::test(TwoFactorSetup::class);
        $component->set('data.otp', (new Google2FA)->getCurrentOtp(session('two_factor_setup.secret')))
            ->call('verifyAndEnable');
        $component->call('complete')->assertSet('view', 'enabled');

        $component->set('disablePassword', 'nope')->call('disable')
            ->assertHasErrors('disablePassword')
            ->assertSet('view', 'enabled'); // still on

        $this->assertNotNull($user->fresh()->app_authentication_secret);
    }

    /** Enrol the current user and return the live component, parked on 'enabled'. */
    private function enrol(User $user): Testable
    {
        $component = Livewire::test(TwoFactorSetup::class);
        $component->set('data.otp', (new Google2FA)->getCurrentOtp(session('two_factor_setup.secret')))
            ->call('verifyAndEnable');
        $component->call('complete')->assertSet('view', 'enabled');

        return $component;
    }

    public function test_verify_and_enable_is_refused_once_already_enrolled(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = $this->enrol($user);
        $secret = $user->fresh()->app_authentication_secret;

        // A stolen session must not be able to rotate a live TOTP secret.
        $component->set('data.otp', '123456')->call('verifyAndEnable')->assertHasErrors('data.otp');

        $this->assertSame($secret, $user->fresh()->app_authentication_secret);
    }

    public function test_cancel_setup_does_nothing_once_already_enrolled(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = $this->enrol($user);
        $secret = $user->fresh()->app_authentication_secret;

        $component->call('cancelSetup');

        $this->assertSame($secret, $user->fresh()->app_authentication_secret);
        $this->assertNull(session('two_factor_setup')); // no fresh pending secret minted
    }

    public function test_disable_locks_out_after_repeated_wrong_passwords(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = $this->enrol($user);

        for ($i = 0; $i < 5; $i++) {
            $component->set('disablePassword', 'wrong')->call('disable');
        }

        $this->assertTrue(RateLimiter::tooManyAttempts('two-factor-disable:'.$user->getKey(), 5));

        // Even the CORRECT password is now blocked by the throttle.
        $component->set('disablePassword', 'password')->call('disable')
            ->assertHasErrors('disablePassword')
            ->assertSet('view', 'enabled');

        $this->assertNotNull($user->fresh()->app_authentication_secret); // still on
    }

    public function test_view_property_is_locked_against_client_mutation(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $component = Livewire::test(TwoFactorSetup::class);

        try {
            // #[Locked] — Livewire rejects (throws); a silent no-op is equally fine.
            $component->set('view', 'enabled');
        } catch (\Throwable $e) {
            // expected
        }

        $component->assertSet('view', 'setup');
    }

    public function test_pending_secret_is_not_reused_across_a_different_user(): void
    {
        $userA = User::factory()->admin()->create();
        $userB = User::factory()->admin()->create();

        // User A primes a pending secret on this browser session.
        $this->actingAs($userA);
        Livewire::test(TwoFactorSetup::class);
        $secretA = session('two_factor_setup.secret');
        $this->assertNotNull($secretA);

        // User B logs in on the SAME session (Filament carries data across
        // session()->regenerate()) and mounts the component.
        $this->actingAs($userB);
        Livewire::test(TwoFactorSetup::class);
        $secretB = session('two_factor_setup.secret');

        // B must get a fresh secret of their own — never A's pending secret.
        $this->assertNotNull($secretB);
        $this->assertNotSame($secretA, $secretB);
    }
}
