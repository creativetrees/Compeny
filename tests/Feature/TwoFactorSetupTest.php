<?php

namespace Tests\Feature;

use App\Livewire\TwoFactorSetup;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
