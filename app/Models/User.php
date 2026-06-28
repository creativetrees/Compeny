<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'username', 'email', 'nik', 'phone', 'password'])]
#[Hidden(['password', 'remember_token', 'nik'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Resolve the credential field for a login value that may be a username or
     * an email address. Usernames are stored lower-case, so the value is
     * normalised here to keep the login case-insensitive.
     *
     * @return array{0: string, 1: string} [field, value]
     */
    public static function resolveLoginField(string $login): array
    {
        return filter_var($login, FILTER_VALIDATE_EMAIL)
            ? ['email', $login]
            : ['username', Str::lower($login)];
    }

    /**
     * Determine whether the user may access the Filament admin panel.
     *
     * Access is restricted to the email domains listed in config/panel.php
     * (env PANEL_ALLOWED_EMAIL_DOMAINS). An empty allow-list denies everyone
     * — the gate fails closed.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $domains = array_map('strtolower', (array) config('panel.allowed_email_domains', []));

        if ($domains === []) {
            return false;
        }

        return in_array(Str::lower(Str::after($this->email, '@')), $domains, true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
