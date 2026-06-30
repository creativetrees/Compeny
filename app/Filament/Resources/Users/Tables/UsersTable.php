<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->label('Name')
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
                    ->copyMessage('Email copied')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Role(s)')
                    ->badge()
                    ->icon('heroicon-m-shield-check')
                    ->color('success')
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->placeholder('— no roles yet'),

                IconColumn::make('email_verified_at')
                    ->label('Verification')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-badge')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn ($record): string => $record->email_verified_at ? 'Email verified' : 'Not verified')
                    ->sortable(),

                IconColumn::make('mfa')
                    ->label('2FA')
                    ->boolean()
                    ->trueIcon('heroicon-s-lock-closed')
                    ->falseIcon('heroicon-s-lock-open')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn ($record): string => ((bool) $record->has_email_authentication || filled($record->app_authentication_secret)) ? '2FA enabled' : '2FA disabled')
                    ->state(fn ($record): bool => (bool) $record->has_email_authentication || filled($record->app_authentication_secret)),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? '•••• •••• •••• '.Str::substr($state, -4) : '—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Role(s)')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('email_verified_at')
                    ->label('Email verification')
                    ->placeholder('All')
                    ->trueLabel('Verified')
                    ->falseLabel('Not verified')
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
            ->emptyStateHeading('No users yet')
            ->emptyStateDescription('Add an admin or team member to access this panel.');
    }
}
