<?php

namespace App\Filament\Auth;

use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use App\Support\PasswordResetOtp;
use Filament\Actions\Action;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Native-Filament forgot-password wizard (Filament\Schemas\Components\Wizard):
 *   Step 1  email + username  → afterValidation emails a one-time code (non-enumerating)
 *   Step 2  email OTP + NIK    → afterValidation verifies inbox possession (+ NIK if on file)
 *   Step 3  new password       → the wizard submit resets it, bound to the verified session
 *
 * The wizard UI is 100% Filament; the security (server-authoritative, hashed,
 * expiring, single-use, attempt-capped code) lives in App\Support\PasswordResetOtp.
 * Per security review C1, the emailed OTP is always required — a NIK alone (a
 * low-entropy, partly-public KTP) never authorises a reset.
 */
class ForgotPassword extends RequestPasswordReset
{
    public function mount(): void
    {
        PasswordResetOtp::clear();

        parent::mount();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Identitas')
                        ->description('Email & username')
                        ->schema([
                            TextInput::make('email')->label('Email')->email()->required()->autofocus(),
                            TextInput::make('username')->label('Username')->required(),
                        ])
                        ->afterValidation(fn () => $this->sendCode()),

                    Step::make('Verifikasi')
                        ->description('Kode OTP & KTP')
                        ->schema([
                            OneTimeCodeInput::make('otp')
                                ->label('Kode OTP (6 digit, dikirim ke email)')
                                ->required(),
                            TextInput::make('nik')
                                ->label('NIK (No. KTP)')
                                ->length(16)
                                ->rule('regex:/^\d{16}$/')
                                ->visible(fn (): bool => PasswordResetOtp::requiresNik())
                                ->required(fn (): bool => PasswordResetOtp::requiresNik()),
                        ])
                        ->afterValidation(fn () => $this->verifyCode()),

                    Step::make('Password baru')
                        ->description('Buat password baru')
                        ->schema([
                            TextInput::make('password')
                                ->label('Password baru')
                                ->password()
                                ->revealable()
                                ->required()
                                ->rule(Password::default())
                                ->same('password_confirmation'),
                            TextInput::make('password_confirmation')
                                ->label('Ulangi password baru')
                                ->password()
                                ->revealable()
                                ->required()
                                ->dehydrated(false),
                        ]),
                ])
                    ->submitAction($this->getRequestFormAction()),
            ]);
    }

    /** The footer submit is removed — the wizard renders the submit on its last step. */
    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRequestFormAction(): Action
    {
        return Action::make('request')
            ->label('Simpan password baru')
            ->submit('request');
    }

    /** Final wizard submit: only reachable after both steps validated. */
    public function request(): void
    {
        $user = PasswordResetOtp::verifiedUser();

        if (! $user) {
            PasswordResetOtp::clear();

            throw ValidationException::withMessages([
                'data.password' => 'Sesi reset kedaluwarsa. Silakan ulangi dari awal.',
            ]);
        }

        $user->forceFill(['password' => Hash::make($this->data['password'])])->save();
        PasswordResetOtp::clear();

        Notification::make()
            ->title('Password berhasil diperbarui. Silakan login dengan password baru.')
            ->success()
            ->send();

        $this->redirect(Filament::getLoginUrl());
    }

    protected function sendCode(): void
    {
        // Per-IP throttle on top of the per-account issue cap (PasswordResetOtp):
        // stops an attacker with a list of email+username pairs from spraying OTP
        // emails account by account. Over the limit → same non-enumerating outcome.
        $ipKey = 'pw-reset-ip:'.request()->ip();

        if (! RateLimiter::tooManyAttempts($ipKey, 10)) {
            RateLimiter::hit($ipKey, 60);

            $user = User::query()
                ->where('email', $this->data['email'] ?? null)
                ->whereRaw('LOWER(username) = ?', [Str::lower((string) ($this->data['username'] ?? ''))])
                ->first();

            // Gate on real panel access (roles), matching User::canAccessPanel(), and
            // never hand the plaintext OTP to a mailer that would write it to a log.
            if ($user && $user->roles()->exists() && ! $this->mailerLeaksToLog()) {
                $code = PasswordResetOtp::issue($user);

                if ($code !== null) {
                    Mail::to($user->email)->send(new PasswordResetOtpMail($user->name, $code));
                }
            }
        }

        // Non-enumerating: identical outcome (and we never throw) whether or not
        // the account exists, so step 1 cannot be used to probe for valid users.
        Notification::make()
            ->title('Jika data cocok, kode OTP telah dikirim ke email Anda.')
            ->success()
            ->send();
    }

    /** In production, the log/array mailers would write the plaintext OTP to disk. */
    private function mailerLeaksToLog(): bool
    {
        return app()->environment('production')
            && in_array(config('mail.default'), ['log', 'array'], true);
    }

    protected function verifyCode(): void
    {
        if (! PasswordResetOtp::verify((string) ($this->data['otp'] ?? ''), $this->data['nik'] ?? null)) {
            throw ValidationException::withMessages([
                'data.otp' => 'Kode atau NIK salah, atau kode sudah kedaluwarsa.',
            ]);
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Lupa password';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Reset password admin';
    }
}
