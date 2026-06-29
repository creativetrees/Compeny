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

    public function test_mount_primes_a_pending_secret_and_renders_the_qr_inline(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(TwoFactorSetup::class)
            ->assertSet('enabled', false)
            ->assertSee('Aktifkan dengan aplikasi authenticator');

        $this->assertNotNull(session('two_factor_setup.secret'));
    }

    public function test_confirm_with_a_valid_code_enables_2fa(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = Livewire::test(TwoFactorSetup::class);
        $code = (new Google2FA)->getCurrentOtp(session('two_factor_setup.secret'));

        $component->set('code', $code)
            ->call('confirm')
            ->assertHasNoErrors()
            ->assertSet('enabled', true);

        $this->assertNull(session('two_factor_setup'));
        $this->assertNotNull($user->fresh()->app_authentication_secret);
    }

    public function test_confirm_with_an_invalid_code_is_rejected(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        Livewire::test(TwoFactorSetup::class)
            ->set('code', '000000')
            ->call('confirm')
            ->assertHasErrors('code')
            ->assertSet('enabled', false);

        $this->assertNull($user->fresh()->app_authentication_secret);
    }

    public function test_disable_turns_2fa_off(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $component = Livewire::test(TwoFactorSetup::class);
        $component->set('code', (new Google2FA)->getCurrentOtp(session('two_factor_setup.secret')))
            ->call('confirm')
            ->assertSet('enabled', true);

        $component->call('disable')->assertSet('enabled', false);

        $this->assertNull($user->fresh()->app_authentication_secret);
    }
}
