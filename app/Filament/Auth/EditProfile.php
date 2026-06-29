<?php

namespace App\Filament\Auth;

use App\Models\User;
use App\Support\Format;
use Closure;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Custom profile: Nama lengkap, Username, Email, No. HP, KTP (NIK) — no password
 * field (passwords are changed only via the "Lupa password" flow). KTP is
 * required, which the RequireNik middleware enforces on first login.
 */
class EditProfile extends BaseEditProfile
{
    public function getTitle(): string|Htmlable
    {
        return 'Profil Saya';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Profil Saya';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Kelola data pribadi & keamanan akun Anda di sini.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pribadi')
                    ->description('Data identitas akun. Password diatur lewat fitur "Lupa password" di halaman login, bukan di sini.')
                    ->aside()
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama lengkap')
                            ->required()
                            ->maxLength(120)
                            ->prefixIcon('heroicon-m-user')
                            ->columnSpan(1),
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->minLength(3)
                            ->maxLength(30)
                            ->rule('regex:/^[a-z][a-z0-9_.-]{2,29}$/')
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-at-symbol')
                            ->helperText('Dipakai untuk login.')
                            ->columnSpan(1),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-m-envelope')
                            ->columnSpan(1),
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
                            ->rule(fn (): Closure => $this->phoneRule())
                            ->columnSpan(1),
                        TextInput::make('nik')
                            ->label('NIK (No. KTP)')
                            ->required()
                            ->mask('9999-9999-9999-9999')
                            ->placeholder('1234-5678-9012-3456')
                            ->prefixIcon('heroicon-m-identification')
                            ->helperText('Wajib diisi — 16 digit, otomatis menjadi 1234-5678-9012-3456.')
                            ->formatStateUsing(fn (?string $state): ?string => Format::nikMasked($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => Format::nik($state))
                            ->rule(fn (): Closure => $this->nikRule())
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function nikRule(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail): void {
            $digits = Format::digits($value);

            if (strlen($digits) !== 16) {
                $fail('NIK harus tepat 16 digit angka.');

                return;
            }

            if (User::query()->where('nik', $digits)->whereKeyNot(auth()->id())->exists()) {
                $fail('NIK ini sudah terdaftar.');
            }
        };
    }

    protected function phoneRule(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail): void {
            $national = Format::phoneNational($value);

            if (! preg_match('/^8[1-9][0-9]{6,10}$/', $national)) {
                $fail('Nomor HP tidak valid. Contoh: 081212350164 atau +6281212350164.');

                return;
            }

            if (User::query()->where('phone', Format::phoneId($value))->whereKeyNot(auth()->id())->exists()) {
                $fail('Nomor HP ini sudah terdaftar.');
            }
        };
    }
}
