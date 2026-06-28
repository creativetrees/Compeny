<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Support\Format;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                        Tab::make('Identitas')
                            ->icon('heroicon-o-identification')
                            ->columns(6)
                            ->schema([
                                // Row 1 — two across.
                                TextInput::make('name')
                                    ->label('Nama lengkap')
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
                                    ->helperText('Huruf kecil, angka, titik, garis bawah/strip; diawali huruf. Dipakai untuk login.')
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
                                    ->label('NIK (No. KTP)')
                                    ->required()
                                    ->mask('9999-9999-9999-9999')
                                    ->placeholder('1234-5678-9012-3456')
                                    ->prefixIcon('heroicon-m-identification')
                                    ->helperText('16 digit — otomatis menjadi 1234-5678-9012-3456.')
                                    ->formatStateUsing(fn (?string $state): ?string => Format::nikMasked($state))
                                    ->dehydrateStateUsing(fn (?string $state): ?string => Format::nik($state))
                                    ->rule(fn (?Model $record): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                        $digits = Format::digits($value);

                                        if (strlen($digits) !== 16) {
                                            $fail('NIK harus tepat 16 digit angka.');

                                            return;
                                        }

                                        $taken = User::query()
                                            ->where('nik', $digits)
                                            ->when($record, fn (Builder $q): Builder => $q->whereKeyNot($record->getKey()))
                                            ->exists();

                                        if ($taken) {
                                            $fail('NIK ini sudah terdaftar.');
                                        }
                                    })
                                    ->columnSpan(2),
                                TextInput::make('phone')
                                    ->label('No. HP')
                                    ->tel()
                                    ->required()
                                    ->maxLength(25)
                                    ->placeholder('0812 1235 0164')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->helperText('Otomatis menjadi +62 812 1235 0164.')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('phone', Format::phoneId($state)))
                                    ->formatStateUsing(fn (?string $state): ?string => Format::phoneId($state))
                                    ->dehydrateStateUsing(fn (?string $state): ?string => Format::phoneId($state))
                                    ->rule(fn (?Model $record): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                        $national = Format::phoneNational($value);

                                        if (! preg_match('/^8[1-9][0-9]{6,10}$/', $national)) {
                                            $fail('Nomor HP tidak valid. Contoh: 081212350164 atau +6281212350164.');

                                            return;
                                        }

                                        $taken = User::query()
                                            ->where('phone', Format::phoneId($value))
                                            ->when($record, fn (Builder $q): Builder => $q->whereKeyNot($record->getKey()))
                                            ->exists();

                                        if ($taken) {
                                            $fail('Nomor HP ini sudah terdaftar.');
                                        }
                                    })
                                    ->columnSpan(2),
                            ]),

                        Tab::make('Keamanan')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->prefixIcon('heroicon-m-key')
                                    ->helperText('Minimal 8 karakter. Kosongkan saat mengedit bila tidak ingin mengganti password.'),
                            ]),
                    ]),
            ]);
    }
}
