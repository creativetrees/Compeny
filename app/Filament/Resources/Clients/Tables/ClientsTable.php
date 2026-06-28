<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(26)
                    ->extraImgAttributes(['class' => 'object-contain'])
                    ->placeholder('— text —'),
                TextColumn::make('name')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn ($record) => $record->logo_path ? null : 'Text logo'),
                TextColumn::make('website_url')
                    ->label('Link')
                    ->icon('heroicon-m-link')
                    ->url(fn ($record) => $record->website_url, true)
                    ->color('primary')
                    ->limit(34)
                    ->placeholder('—'),
                IconColumn::make('is_featured')
                    ->label('Marquee')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-s-x-mark'),
                TextColumn::make('sort')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
