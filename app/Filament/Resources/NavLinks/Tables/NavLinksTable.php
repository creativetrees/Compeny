<?php

namespace App\Filament\Resources\NavLinks\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NavLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('location')
                    ->badge()
                    ->searchable(),
                TextColumn::make('label')
                    ->searchable(),
                TextColumn::make('url')
                    ->icon('heroicon-m-link')
                    ->searchable(),
                TextColumn::make('sort')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->options([
                        'header' => 'Header',
                        'footer_studio' => 'Footer · Studio',
                        'footer_company' => 'Footer · Company',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-link')
            ->emptyStateHeading('No navigation links yet')
            ->emptyStateDescription('Add links to show in the header and footer navigation.')
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
