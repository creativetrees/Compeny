<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

/**
 * Panel login that accepts either a username or an email address.
 *
 * The single "login" field is matched against the email column when it looks
 * like an email, and against the username column otherwise. Everything else
 * (rate limiting, multi-factor, the canAccessPanel gate) is inherited from
 * Filament's base login page unchanged.
 */
class Login extends BaseLogin
{
    /**
     * Replace the default email-only field with a username-or-email field.
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('Username atau Email'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * Build the auth credentials, resolving username vs. email from the input.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        [$field, $value] = User::resolveLoginField((string) $data['login']);

        return [
            $field => $value,
            'password' => $data['password'],
        ];
    }

    /**
     * Attach the "failed login" error to the login field instead of "email".
     */
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }
}
