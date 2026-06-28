<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

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
                                TextInput::make('name')
                                    ->label('Nama lengkap')
                                    ->required()
                                    ->maxLength(120)
                                    ->prefixIcon('heroicon-m-user')
                                    ->columnSpan(2),
                                TextInput::make('username')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(30)
                                    ->rule('regex:/^[a-z][a-z0-9_.-]{2,29}$/')
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-at-symbol')
                                    ->helperText('Huruf kecil, angka, titik, garis bawah/strip; diawali huruf. Dipakai untuk login.')
                                    ->columnSpan(2),
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
                                    ->length(16)
                                    ->rule('regex:/^\d{16}$/')
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-identification')
                                    ->helperText('Nomor Induk Kependudukan — tepat 16 digit angka.')
                                    ->columnSpan(3),
                                TextInput::make('phone')
                                    ->label('No. HP')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20)
                                    ->rule('regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/')
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-m-phone')
                                    ->helperText('Contoh: 081234567890 atau +6281234567890.')
                                    ->columnSpan(3),
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
