<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Livewire\TwoFactorSetup;
use App\Models\User;
use App\Support\Format;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Identity')
                            ->icon('heroicon-o-identification')
                            ->columns(6)
                            ->schema([
                                // Row 1 — two across.
                                TextInput::make('name')
                                    ->label('Full name')
                                    ->required()
                                    ->maxLength(120)
                                    ->prefixIcon('heroicon-m-user')
                                    ->columnSpan(3),
                                TextInput::make('username')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(30)
                                    ->rule('regex:/^[a-z][a-z0-9_.-]{2,29}$/')
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-at-symbol')
                                    ->helperText('Lowercase letters, numbers, dots, underscores/hyphens; must start with a letter. Used for login.')
                                    ->columnSpan(3),

                                // Row 2 — three across.
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->columnSpan(2),
                                TextInput::make('nik')
                                    ->label('NIK (National ID)')
                                    ->required()
                                    ->mask('9999-9999-9999-9999')
                                    ->placeholder('1234-5678-9012-3456')
                                    ->prefixIcon('heroicon-m-identification')
                                    ->helperText('16 digits — auto-formatted to 1234-5678-9012-3456.')
                                    ->formatStateUsing(fn (?string $state): ?string => Format::nikMasked($state))
                                    ->dehydrateStateUsing(fn (?string $state): ?string => Format::nik($state))
                                    ->rule(fn (?Model $record): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                        $digits = Format::digits($value);

                                        if (strlen($digits) !== 16) {
                                            $fail('NIK must be exactly 16 digits.');

                                            return;
                                        }

                                        $taken = User::query()
                                            ->where('nik', $digits)
                                            ->when($record, fn (Builder $q): Builder => $q->whereKeyNot($record->getKey()))
                                            ->exists();

                                        if ($taken) {
                                            $fail('This NIK is already registered.');
                                        }
                                    })
                                    ->columnSpan(2),
                                TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(25)
                                    ->placeholder('0812 1235 0164')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->helperText('Auto-formatted to +62 812 1235 0164.')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('phone', Format::phoneId($state)))
                                    ->formatStateUsing(fn (?string $state): ?string => Format::phoneId($state))
                                    ->dehydrateStateUsing(fn (?string $state): ?string => Format::phoneId($state))
                                    ->rule(fn (?Model $record): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                        $national = Format::phoneNational($value);

                                        if (! preg_match('/^8[1-9][0-9]{6,10}$/', $national)) {
                                            $fail('Invalid phone number. Example: 081212350164 or +6281212350164.');

                                            return;
                                        }

                                        $taken = User::query()
                                            ->where('phone', Format::phoneId($value))
                                            ->when($record, fn (Builder $q): Builder => $q->whereKeyNot($record->getKey()))
                                            ->exists();

                                        if ($taken) {
                                            $fail('This phone number is already registered.');
                                        }
                                    })
                                    ->columnSpan(2),
                            ]),

                        Tab::make('Security')
                            ->icon('heroicon-o-lock-closed')
                            ->columns(3)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->confirmed()
                                    ->live(onBlur: true)
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->prefixIcon('heroicon-m-key')
                                    ->helperText('Minimum 8 characters. Leave empty when editing to keep the current one.'),
                                TextInput::make('password_confirmation')
                                    ->label('Confirm password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation, callable $get): bool => $operation === 'create' || filled($get('password')))
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-m-key')
                                    ->helperText('Repeat exactly the same password.'),
                                Select::make('roles')
                                    ->label('Role(s)')
                                    ->relationship(
                                        name: 'roles',
                                        titleAttribute: 'name',
                                        // Non-developers cannot even see the developer role in the picker.
                                        modifyQueryUsing: fn (Builder $query): Builder => auth()->user()?->hasRole('developer')
                                            ? $query
                                            : $query->whereKeyNot(Role::where('name', 'developer')->value('id') ?? 0),
                                    )
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Determines access rights. "Developer" = full access — can only be granted by another Developer.')
                                    // Hard stop on privilege tampering: a non-developer can neither grant the
                                    // developer role to anyone, nor strip it from an existing developer.
                                    ->rule(fn (?Model $record): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                        $developerId = Role::where('name', 'developer')->value('id');

                                        // Developers may manage any roles; only non-developers are constrained.
                                        if (! $developerId || auth()->user()?->hasRole('developer')) {
                                            return;
                                        }

                                        $submitted = array_map('intval', (array) $value);

                                        // …cannot GRANT developer to anyone…
                                        if (in_array((int) $developerId, $submitted, true)) {
                                            $fail('Only a Developer can grant the Developer role.');

                                            return;
                                        }

                                        // …and cannot STRIP developer from an existing developer (would revoke access).
                                        if ($record instanceof User && $record->hasRole('developer')) {
                                            $fail('Only a Developer can change the roles of a Developer user.');
                                        }
                                    }),
                            ]),

                        // Native Filament 2FA — only on your OWN account (the TOTP secret
                        // belongs to the account owner; admins can't enrol it for others).
                        // Inline setup component shows the QR directly (no modal).
                        Tab::make('2FA')
                            ->icon('heroicon-o-shield-check')
                            ->visible(fn (?User $record): bool => Filament::hasMultiFactorAuthentication() && $record?->getKey() === Filament::auth()->id())
                            ->schema([
                                Livewire::make(TwoFactorSetup::class)->key('two-factor-setup'),
                            ]),
                    ]),
            ]);
    }
}
