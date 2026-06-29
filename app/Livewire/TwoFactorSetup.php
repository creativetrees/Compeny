<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Inline authenticator-app 2FA setup, rendered as two clean screens that mirror
 * the requested design: "Scan QR code" (QR + manual key + verification code) and
 * then "Save backup codes" (the recovery codes + download).
 *
 * Every security operation is delegated to Filament's own AppAuthentication
 * provider (secret / QR / verify / recovery codes). The pending secret and the
 * plaintext recovery codes live ONLY in the server session — never in a public,
 * client-tamperable Livewire property — so a client can't swap in a secret of
 * their own before confirming.
 *
 * Two pieces of state drive what is rendered:
 *   $view  — 'setup' (enrolment) | 'enabled' (active-state panel)
 *   $step  — 1 (scan + verify) | 2 (save backup codes), only meaningful in setup
 * The provider's isEnabled() flag is NEVER used to pick the surface: step 1 saves
 * the secret, yet the UI must stay on screen for step 2 (recovery codes). $view
 * flips to 'enabled' only once the user finishes step 2.
 */
class TwoFactorSetup extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    private const SESSION_KEY = 'two_factor_setup';

    /** @var array<string, mixed> */
    public ?array $data = [];

    /**
     * Which surface to render: the enrolment flow or the active-state panel.
     * #[Locked] — only the server may flip this; a client must not be able to
     * force the setup view on an already-enrolled account (secret-rotation guard).
     */
    #[Locked]
    public string $view = 'setup';

    /**
     * Which enrolment screen is visible: 1 = scan + verify, 2 = save backup codes.
     * #[Locked] — clients must not jump to step 2 to reveal the pending recovery
     * codes before the OTP has actually been verified.
     */
    #[Locked]
    public int $step = 1;

    /** Current-password re-auth for disabling 2FA (transient; reset after use). */
    public ?string $disablePassword = null;

    public function mount(): void
    {
        $this->view = $this->provider()->isEnabled(Filament::auth()->user()) ? 'enabled' : 'setup';

        if ($this->view === 'setup') {
            $this->primePendingSecret();
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components(match (true) {
                $this->view === 'enabled' => [$this->enabledSection()],
                $this->step === 2 => $this->backupCodesStep(),
                default => $this->scanStep(),
            });
    }

    /**
     * Screen 1 — a native Filament Section laid out as a responsive two-column
     * grid: scan the QR / copy the key on the left, type the 6-digit code on the
     * right. It fills the panel width on desktop and stacks on mobile, matching
     * how the rest of the form behaves.
     */
    protected function scanStep(): array
    {
        return [
            Section::make('Autentikasi dua faktor (2FA)')
                ->description('Minta kode dari aplikasi authenticator setiap kali login — lapisan keamanan kedua untuk akun Anda.')
                ->icon('heroicon-o-shield-check')
                ->columns(['default' => 1, 'lg' => 2])
                ->schema([
                    ViewField::make('scan')
                        ->view('filament.two-factor.qr')
                        ->viewData([
                            'qrCodeDataUri' => $this->qrCodeDataUri(),
                            'setupKey' => $this->pending('secret'),
                        ]),
                    Group::make([
                        ViewField::make('verifyHeader')
                            ->view('filament.two-factor.verify-header'),
                        OneTimeCodeInput::make('otp')
                            ->hiddenLabel()
                            ->required(),
                    ]),
                    ViewField::make('actions')
                        ->view('filament.two-factor.step1-actions')
                        ->columnSpanFull(),
                ]),
        ];
    }

    /** Screen 2 — the plaintext recovery codes, shown once, in a native Section. */
    protected function backupCodesStep(): array
    {
        return [
            Section::make('Simpan kode cadangan')
                ->description('Simpan kode ini di tempat aman — dipakai untuk masuk bila kehilangan perangkat authenticator. Setiap kode hanya berlaku sekali.')
                ->icon('heroicon-o-key')
                ->schema([
                    ViewField::make('backup')
                        ->view('filament.two-factor.recovery-codes')
                        ->viewData([
                            'recoveryCodes' => $this->pending('recoveryCodes') ?? [],
                            'completing' => true,
                        ]),
                ]),
        ];
    }

    protected function enabledSection(): Section
    {
        $user = Filament::auth()->user();

        return Section::make('Two-factor authentication aktif')
            ->description('Setiap login akan meminta kode dari aplikasi authenticator Anda.')
            ->icon('heroicon-o-shield-check')
            ->iconColor('success')
            ->schema([
                ViewField::make('enabled')
                    ->view('filament.two-factor.enabled')
                    ->viewData([
                        // Used codes are consumed at login, so this count falls over
                        // time — that is how the UI reflects "spent" recovery codes.
                        'remainingCount' => count($this->provider()->getRecoveryCodes($user)),
                        // Freshly-regenerated plaintext codes, shown once then cleared.
                        'freshRecoveryCodes' => $this->pending('recoveryCodes') ?? [],
                    ]),
            ])
            ->footerActions([
                Action::make('regenerateRecoveryCodes')
                    ->label('Buat ulang kode pemulihan')
                    ->icon('heroicon-m-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Buat ulang kode pemulihan?')
                    ->modalDescription('Semua kode pemulihan lama langsung berhenti berfungsi dan diganti dengan set yang baru.')
                    ->modalSubmitActionLabel('Buat ulang')
                    ->action(fn () => $this->regenerateRecoveryCodes()),
            ]);
    }

    /**
     * Screen 1 → screen 2 transition: verify the typed code against the SESSION
     * secret, then persist. The session is deliberately kept intact so screen 2
     * can still display the plaintext recovery codes.
     */
    public function verifyAndEnable(): void
    {
        // Already enrolled? Refuse — never overwrite a live secret from this flow
        // (defence-in-depth alongside the #[Locked] $view; blocks secret rotation).
        if ($this->provider()->isEnabled(Filament::auth()->user())) {
            throw ValidationException::withMessages([
                'data.otp' => '2FA sudah aktif untuk akun ini.',
            ]);
        }

        $secret = $this->pending('secret');
        $codes = $this->pending('recoveryCodes') ?? [];
        $otp = (string) ($this->data['otp'] ?? '');

        if (blank($otp)) {
            throw ValidationException::withMessages([
                'data.otp' => 'Masukkan 6 digit kode dari aplikasi authenticator Anda.',
            ]);
        }

        if (blank($secret) || ! $this->provider()->verifyCode($otp, $secret)) {
            throw ValidationException::withMessages([
                'data.otp' => 'Kode salah atau kedaluwarsa. Pastikan jam perangkat tepat lalu coba lagi.',
            ]);
        }

        $user = Filament::auth()->user();
        $this->provider()->saveSecret($user, $secret);
        $this->provider()->saveRecoveryCodes($user, $codes);

        $this->step = 2;
    }

    /** Cancel screen 1 — nothing is persisted yet, so just mint a fresh secret. */
    public function cancelSetup(): void
    {
        // Never mint a fresh pending secret for an already-enrolled account —
        // that secret would be rendered into the QR and could rotate the factor.
        if ($this->provider()->isEnabled(Filament::auth()->user())) {
            return;
        }

        session()->forget(self::SESSION_KEY);
        $this->primePendingSecret();
        $this->reset('data');
        $this->step = 1;

        Notification::make()->title('Penyiapan diatur ulang.')->send();
    }

    /** Final submit on screen 2: only finishes once the secret is genuinely saved. */
    public function complete(): void
    {
        if (! $this->provider()->isEnabled(Filament::auth()->user())) {
            return;
        }

        session()->forget(self::SESSION_KEY);
        $this->view = 'enabled';
        $this->step = 1;
        $this->reset('data');

        Notification::make()->title('Two-factor authentication aktif.')->success()->send();
    }

    /** Throw away the un-confirmed secret + codes and mint a fresh set. */
    public function regenerate(): void
    {
        // Derive state from the provider, never the client-tamperable $view prop.
        if ($this->provider()->isEnabled(Filament::auth()->user())) {
            return;
        }

        session()->forget(self::SESSION_KEY);
        $this->primePendingSecret();
        $this->step = 1;
        $this->data['otp'] = null;

        Notification::make()->title('QR & kode pemulihan baru dibuat.')->success()->send();
    }

    /** Replace the recovery codes of an already-enabled account (old set dies). */
    public function regenerateRecoveryCodes(): void
    {
        $user = Filament::auth()->user();

        if (! $this->provider()->isEnabled($user)) {
            return;
        }

        $codes = $this->provider()->generateRecoveryCodes();
        $this->provider()->saveRecoveryCodes($user, $codes);

        // Stash the plaintext once so the panel can show them for copy/download,
        // keyed to the user so the ownership guard in pending() accepts them.
        session()->put(self::SESSION_KEY, [
            'user_id' => Filament::auth()->id(),
            'recoveryCodes' => $codes,
        ]);

        Notification::make()->title('Kode pemulihan baru dibuat. Simpan sekarang.')->success()->send();
    }

    public function disable(): void
    {
        $user = Filament::auth()->user();
        $throttleKey = 'two-factor-disable:'.$user->getAuthIdentifier();

        // Throttle the re-auth: a stolen session must not be able to brute-force
        // the account password by hammering this endpoint (5 tries / minute).
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'disablePassword' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($throttleKey).' detik.',
            ]);
        }

        // Re-auth before stripping the factor — a stolen session alone can't
        // silently turn 2FA off without the account password.
        if (! Hash::check((string) $this->disablePassword, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->disablePassword = null; // never retain the wrong password in the snapshot

            throw ValidationException::withMessages([
                'disablePassword' => 'Password Anda salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $this->provider()->saveSecret($user, null);
        $this->provider()->saveRecoveryCodes($user, null);

        session()->forget(self::SESSION_KEY);
        $this->view = 'setup';
        $this->step = 1;
        $this->primePendingSecret();
        $this->reset('data', 'disablePassword');

        Notification::make()->title('Two-factor authentication dinonaktifkan.')->warning()->send();
    }

    public function downloadRecoveryCodes(): StreamedResponse
    {
        // Only the freshly-generated PLAINTEXT codes are downloadable. Once saved
        // they are stored bcrypt-hashed and can never be shown again, so there is
        // nothing to hand out post-enable (we never expose the hashes).
        $codes = $this->pending('recoveryCodes');

        abort_if(blank($codes), 404);

        return response()->streamDownload(
            fn () => print ("Creative Trees Group — Kode Pemulihan 2FA\n".str_repeat('=', 44)."\n\n".implode("\n", $codes)."\n\nSimpan file ini di tempat aman. Setiap kode hanya bisa dipakai sekali.\n"),
            'creative-trees-2fa-recovery-codes.txt',
            ['Content-Type' => 'text/plain'],
        );
    }

    public function render(): View
    {
        return view('livewire.two-factor-setup');
    }

    private function qrCodeDataUri(): ?string
    {
        $secret = $this->pending('secret');

        return filled($secret) ? $this->provider()->generateQrCodeDataUri($secret) : null;
    }

    /**
     * Read a value from the pending-setup session, but ONLY when the stored
     * payload belongs to the current user. Guards against a shared browser whose
     * session carries another user's pending secret across a login.
     */
    private function pending(string $key): mixed
    {
        $stored = session(self::SESSION_KEY);

        if (! is_array($stored) || ($stored['user_id'] ?? null) !== Filament::auth()->id()) {
            return null;
        }

        return $stored[$key] ?? null;
    }

    private function primePendingSecret(): void
    {
        $stored = session(self::SESSION_KEY);

        // Only reuse a pending secret that belongs to the CURRENT user. At login
        // Filament calls session()->regenerate(), which carries the previous
        // user's pending data into the new session; without this ownership check a
        // new user on a shared browser could enrol the previous user's secret.
        if (is_array($stored) && ($stored['user_id'] ?? null) === Filament::auth()->id()) {
            return;
        }

        session()->put(self::SESSION_KEY, [
            'user_id' => Filament::auth()->id(),
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
