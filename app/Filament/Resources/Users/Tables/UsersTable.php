<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->striped()
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->weight(FontWeight::SemiBold)
                    ->icon('heroicon-m-user-circle')
                    ->iconColor('primary')
                    ->description(fn ($record): string => '@'.$record->username)
                    ->searchable(['name', 'username'])
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email disalin')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_admin')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Admin' : 'Member')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-shield-check' : 'heroicon-m-user')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),

                IconColumn::make('email_verified_at')
                    ->label('Verifikasi')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-badge')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('mfa')
                    ->label('2FA')
                    ->boolean()
                    ->trueIcon('heroicon-s-lock-closed')
                    ->falseIcon('heroicon-s-lock-open')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->state(fn ($record): bool => (bool) $record->has_email_authentication || filled($record->app_authentication_secret)),

                TextColumn::make('phone')
                    ->label('No. HP')
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? '•••• •••• •••• '.Str::substr($state, -4) : '—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y')
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_admin')
                    ->label('Peran')
                    ->placeholder('Semua peran')
                    ->trueLabel('Admin')
                    ->falseLabel('Member'),
                TernaryFilter::make('email_verified_at')
                    ->label('Verifikasi email')
                    ->placeholder('Semua')
                    ->trueLabel('Terverifikasi')
                    ->falseLabel('Belum terverifikasi')
                    ->nullable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record): bool => $record->getKey() !== auth()->id()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('Belum ada pengguna')
            ->emptyStateDescription('Tambahkan admin atau anggota tim untuk mengakses panel ini.');
    }
}
