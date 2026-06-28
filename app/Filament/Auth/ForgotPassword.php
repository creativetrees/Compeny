<?php

namespace App\Filament\Auth;

use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use App\Support\PasswordResetOtp;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Secure three-step "forgot password" wizard:
 *   1. email + username → emails a one-time code (non-enumerating)
 *   2. email OTP + NIK   → proves inbox possession (+ NIK when on file)
 *   3. new password      → resets, bound to the verified session
 *
 * Proof of inbox (the OTP) is always required; NIK alone never authorises a
 * reset (security review C1: a 16-digit KTP is low-entropy + partly public).
 */
class ForgotPassword extends RequestPasswordReset
{
    public int $step = 1;

    public function mount(): void
    {
        PasswordResetOtp::clear();

        parent::mount();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components($this->stepComponents());
    }

    /** @return array<Component> */
    protected function stepComponents(): array
    {
        return match ($this->step) {
            2 => array_values(array_filter([
                OneTimeCodeInput::make('otp')
                    ->label('Kode OTP (dikirim ke email)')
                    ->required()
                    ->autofocus(),
                PasswordResetOtp::requiresNik()
                    ? TextInput::make('nik')
                        ->label('NIK (No. KTP)')
                        ->required()
                        ->length(16)
                        ->rule('regex:/^\d{16}$/')
                    : null,
            ])),
            3 => [
                TextInput::make('password')
                    ->label('Password baru')
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(Password::default())
                    ->same('password_confirmation')
                    ->autofocus(),
                TextInput::make('password_confirmation')
                    ->label('Ulangi password baru')
                    ->password()
                    ->revealable()
                    ->required()
                    ->dehydrated(false),
            ],
            default => [
                TextInput::make('email')->label('Email')->email()->required()->autofocus(),
                TextInput::make('username')->label('Username')->required(),
            ],
        };
    }

    public function request(): void
    {
        match ($this->step) {
            2 => $this->verifyCode(),
            3 => $this->resetPassword(),
            default => $this->sendCode(),
        };
    }

    protected function sendCode(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return;
        }

        $data = $this->form->getState();

        $user = User::query()
            ->where('email', $data['email'])
            ->whereRaw('LOWER(username) = ?', [Str::lower((string) $data['username'])])
            ->first();

        if ($user && $user->is_admin) {
            $code = PasswordResetOtp::issue($user);

            if ($code !== null) {
                Mail::to($user->email)->send(new PasswordResetOtpMail($user->name, $code));
            }
        }

        // Non-enumerating: identical outcome whether or not the account exists.
        $this->step = 2;
        $this->form->fill();

        Notification::make()
            ->title('Jika data cocok, kode OTP telah dikirim ke email Anda.')
            ->success()
            ->send();
    }

    protected function verifyCode(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return;
        }

        $data = $this->form->getState();

        if (! PasswordResetOtp::verify((string) ($data['otp'] ?? ''), $data['nik'] ?? null)) {
            throw ValidationException::withMessages([
                'data.otp' => 'Kode atau NIK salah, atau kode sudah kedaluwarsa.',
            ]);
        }

        $this->step = 3;
        $this->form->fill();
    }

    protected function resetPassword(): void
    {
        $data = $this->form->getState();

        $user = PasswordResetOtp::verifiedUser();

        if (! $user) {
            PasswordResetOtp::clear();
            $this->step = 1;
            $this->form->fill();

            throw ValidationException::withMessages([
                'data.email' => 'Sesi reset kedaluwarsa. Silakan ulangi dari awal.',
            ]);
        }

        $user->forceFill(['password' => Hash::make($data['password'])])->save();
        PasswordResetOtp::clear();

        Notification::make()
            ->title('Password berhasil diperbarui. Silakan login dengan password baru.')
            ->success()
            ->send();

        $this->redirect(Filament::getLoginUrl());
    }

    protected function getRequestFormAction(): Action
    {
        return Action::make('request')
            ->label(match ($this->step) {
                2 => 'Verifikasi kode',
                3 => 'Simpan password baru',
                default => 'Kirim kode OTP',
            })
            ->submit('request');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Lupa password';
    }

    public function getHeading(): string|Htmlable|null
    {
        return match ($this->step) {
            2 => 'Masukkan kode OTP',
            3 => 'Buat password baru',
            default => 'Reset password admin',
        };
    }
}
