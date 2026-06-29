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
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Inline authenticator-app 2FA setup, rendered as a NATIVE Filament wizard.
 *
 * Every security operation is delegated to Filament's own AppAuthentication
 * provider (secret / QR / verify / recovery codes). The pending secret and the
 * plaintext recovery codes live ONLY in the server session — never in a public,
 * client-tamperable Livewire property — so a client can't swap in a secret of
 * their own before confirming.
 *
 * The visible surface is driven by the explicit {@see $view} state machine
 * ('setup' | 'enabled'), NEVER by the provider's isEnabled() flag: step 1 saves
 * the secret, yet the wizard must stay on screen for step 2 (recovery codes).
 * $view flips to 'enabled' only once the user finishes the wizard.
 */
class TwoFactorSetup extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    private const SESSION_KEY = 'two_factor_setup';

    /** @var array<string, mixed> */
    public ?array $data = [];

    /** Which surface to render: the enrolment wizard or the active-state panel. */
    public string $view = 'setup';

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
            ->components($this->view === 'enabled'
                ? [$this->enabledSection()]
                : [$this->wizard()]);
    }

    /**
     * Step 1 verifies a live code and saves the secret + recovery codes; step 2
     * shows the plaintext recovery codes once. The wizard is NEVER swapped out
     * based on isEnabled() — only the final "Selesai" submit flips $view.
     */
    protected function wizard(): Wizard
    {
        return Wizard::make([
            Step::make('Verifikasi')
                ->icon('heroicon-o-device-phone-mobile')
                ->schema([
                    Grid::make(['default' => 1, 'md' => 2])
                        ->schema([
                            ViewField::make('qr')
                                ->view('filament.two-factor.qr')
                                ->viewData(['qrCodeDataUri' => $this->qrCodeDataUri()]),
                            Group::make([
                                ViewField::make('manualKey')
                                    ->view('filament.two-factor.manual-key')
                                    ->viewData(['setupKey' => session(self::SESSION_KEY.'.secret')]),
                                OneTimeCodeInput::make('otp')
                                    ->label('Kode 6 digit')
                                    ->required(),
                            ]),
                        ]),
                    Actions::make([
                        Action::make('regenerate')
                            ->label('Buat ulang QR')
                            ->icon('heroicon-m-arrow-path')
                            ->color('gray')
                            ->link()
                            ->action(fn () => $this->regenerate()),
                    ])->alignEnd(),
                ])
                ->afterValidation(fn () => $this->verifyAndEnable()),

            Step::make('Kode Pemulihan')
                ->icon('heroicon-o-key')
                ->schema([
                    ViewField::make('recoveryCodes')
                        ->view('filament.two-factor.recovery-codes')
                        ->viewData([
                            'recoveryCodes' => session(self::SESSION_KEY.'.recoveryCodes', []),
                        ]),
                ]),
        ])
            ->nextAction(fn (Action $action) => $action
                ->label('Konfirmasi & aktifkan')
                ->icon('heroicon-m-check-circle'))
            ->submitAction(new HtmlString(Blade::render(
                <<<'BLADE'
                    <x-filament::button
                        wire:click="complete"
                        wire:target="complete"
                        wire:loading.attr="disabled"
                        color="success"
                        icon="heroicon-m-check-badge"
                    >
                        Selesai
                    </x-filament::button>
                BLADE
            )));
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
                        'freshRecoveryCodes' => session(self::SESSION_KEY.'.recoveryCodes', []),
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
     * Step 1 → step 2 transition: verify the typed code against the SESSION
     * secret, then persist. The session is deliberately kept intact so step 2
     * can still display the plaintext recovery codes.
     */
    public function verifyAndEnable(): void
    {
        $secret = session(self::SESSION_KEY.'.secret');
        $codes = session(self::SESSION_KEY.'.recoveryCodes', []);

        if (blank($secret) || ! $this->provider()->verifyCode((string) ($this->data['otp'] ?? ''), $secret)) {
            throw ValidationException::withMessages([
                'data.otp' => 'Kode salah atau kedaluwarsa. Pastikan jam perangkat tepat lalu coba lagi.',
            ]);
        }

        $user = Filament::auth()->user();
        $this->provider()->saveSecret($user, $secret);
        $this->provider()->saveRecoveryCodes($user, $codes);
    }

    /** Final wizard submit: only finishes once the secret is genuinely saved. */
    public function complete(): void
    {
        if (! $this->provider()->isEnabled(Filament::auth()->user())) {
            return;
        }

        session()->forget(self::SESSION_KEY);
        $this->view = 'enabled';
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

        // Stash the plaintext once so the panel can show them for copy/download.
        session()->put(self::SESSION_KEY.'.recoveryCodes', $codes);

        Notification::make()->title('Kode pemulihan baru dibuat. Simpan sekarang.')->success()->send();
    }

    public function disable(): void
    {
        $user = Filament::auth()->user();

        // Re-auth before stripping the factor — a stolen session alone can't
        // silently turn 2FA off without the account password.
        if (! Hash::check((string) $this->disablePassword, $user->password)) {
            throw ValidationException::withMessages([
                'disablePassword' => 'Password Anda salah.',
            ]);
        }

        $this->provider()->saveSecret($user, null);
        $this->provider()->saveRecoveryCodes($user, null);

        session()->forget(self::SESSION_KEY);
        $this->view = 'setup';
        $this->primePendingSecret();
        $this->reset('data', 'disablePassword');

        Notification::make()->title('Two-factor authentication dinonaktifkan.')->warning()->send();
    }

    public function downloadRecoveryCodes(): StreamedResponse
    {
        // Only the freshly-generated PLAINTEXT codes are downloadable. Once saved
        // they are stored bcrypt-hashed and can never be shown again, so there is
        // nothing to hand out post-enable (we never expose the hashes).
        $codes = session(self::SESSION_KEY.'.recoveryCodes');

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
        $secret = session(self::SESSION_KEY.'.secret');

        return filled($secret) ? $this->provider()->generateQrCodeDataUri($secret) : null;
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
