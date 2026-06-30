<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                ImageColumn::make('cover_path')
                    ->label('Cover')
                    ->disk('public')
                    ->height(40)
                    ->square(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->icon('heroicon-m-rectangle-stack')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->icon('heroicon-m-tag')
                    ->searchable(),
                TextColumn::make('price_label')
                    ->icon('heroicon-m-banknotes')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'published' => 'heroicon-m-check-circle',
                        'draft' => 'heroicon-m-pencil-square',
                        default => 'heroicon-m-flag',
                    })
                    ->searchable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-s-x-mark'),
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
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options(Product::query()->whereNotNull('status')->distinct()->pluck('status', 'status')->all()),
                SelectFilter::make('type')
                    ->options(Product::query()->whereNotNull('type')->distinct()->pluck('type', 'type')->all()),
                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),
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
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Add a product to show it in the catalogue.')
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
