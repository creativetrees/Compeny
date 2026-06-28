<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama lengkap')
                    ->required()
                    ->maxLength(120),
                TextInput::make('username')
                    ->required()
                    ->minLength(3)
                    ->maxLength(30)
                    ->rule('regex:/^[a-z][a-z0-9_.-]{2,29}$/')
                    ->unique(ignoreRecord: true)
                    ->helperText('Huruf kecil, angka, titik, garis bawah/strip; diawali huruf. Dipakai untuk login.'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('nik')
                    ->label('NIK (No. KTP)')
                    ->required()
                    ->length(16)
                    ->rule('regex:/^\d{16}$/')
                    ->unique(ignoreRecord: true)
                    ->helperText('Nomor Induk Kependudukan — tepat 16 digit angka.'),
                TextInput::make('phone')
                    ->label('No. HP')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->rule('regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/')
                    ->unique(ignoreRecord: true)
                    ->helperText('Contoh: 081234567890 atau +6281234567890.'),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('Minimal 8 karakter. Kosongkan saat mengedit bila tidak ingin mengganti password.'),
            ]);
    }
}
