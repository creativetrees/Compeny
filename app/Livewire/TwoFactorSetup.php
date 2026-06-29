<?php

namespace App\Livewire;

use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Inline authenticator-app 2FA setup — shows the QR code directly (no modal),
 * reusing Filament's native AppAuthentication provider for every security
 * operation (secret/QR/verify/recovery codes). The pending secret + recovery
 * codes live in the SERVER session (never a tamperable Livewire property), so a
 * client can't swap in a secret of their own before confirming.
 */
class TwoFactorSetup extends Component
{
    private const SESSION_KEY = 'two_factor_setup';

    public bool $enabled = false;

    #[Validate('required|digits:6')]
    public ?string $code = null;

    public function mount(): void
    {
        $this->enabled = $this->provider()->isEnabled(Filament::auth()->user());

        if (! $this->enabled) {
            $this->primePendingSecret();
        }
    }

    public function confirm(): void
    {
        $this->validate();

        $pending = session(self::SESSION_KEY);

        if (! $pending || ! $this->provider()->verifyCode($this->code, $pending['secret'])) {
            throw ValidationException::withMessages([
                'code' => 'Kode salah atau kedaluwarsa. Pastikan jam perangkat tepat lalu coba lagi.',
            ]);
        }

        $user = Filament::auth()->user();
        $this->provider()->saveSecret($user, $pending['secret']);
        $this->provider()->saveRecoveryCodes($user, $pending['recoveryCodes']);

        session()->forget(self::SESSION_KEY);
        $this->enabled = true;
        $this->reset('code');

        Notification::make()->title('Two-factor authentication aktif.')->success()->send();
    }

    public function disable(): void
    {
        $user = Filament::auth()->user();
        $this->provider()->saveSecret($user, null);
        $this->provider()->saveRecoveryCodes($user, null);

        session()->forget(self::SESSION_KEY);
        $this->enabled = false;
        $this->reset('code');
        $this->primePendingSecret();

        Notification::make()->title('Two-factor authentication dinonaktifkan.')->warning()->send();
    }

    public function render()
    {
        $pending = session(self::SESSION_KEY, []);

        return view('livewire.two-factor-setup', [
            'qrCodeDataUri' => isset($pending['secret']) ? $this->provider()->generateQrCodeDataUri($pending['secret']) : null,
            'setupKey' => $pending['secret'] ?? null,
            'recoveryCodes' => $pending['recoveryCodes'] ?? [],
        ]);
    }

    private function primePendingSecret(): void
    {
        if (session()->has(self::SESSION_KEY)) {
            return;
        }

        session()->put(self::SESSION_KEY, [
            'secret' => $this->provider()->generateSecret(),
            'recoveryCodes' => $this->provider()->generateRecoveryCodes(),
        ]);
    }

    private function provider(): AppAuthentication
    {
        foreach (Filament::getMultiFactorAuthenticationProviders() as $provider) {
            if ($provider instanceof AppAuthentication) {
                return $provider;
            }
        }

        abort(500, 'App authentication is not configured on this panel.');
    }
}
