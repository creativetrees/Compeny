<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

/**
 * Panel login that authenticates by USERNAME + password only (no email login).
 *
 * Usernames are stored lower-case, so the submitted value is normalised before
 * the credential lookup to keep login case-insensitive. Rate limiting,
 * multi-factor auth, and the canAccessPanel gate are inherited from Filament's
 * base login page unchanged.
 */
class Login extends BaseLogin
{
    /**
     * Replace the default email field with a username field.
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('Username'))
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * Authenticate against the username column (case-insensitive).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'username' => Str::lower((string) $data['login']),
            'password' => $data['password'],
        ];
    }

    /**
     * Attach the "failed login" error to the username field instead of "email".
     */
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }
}
