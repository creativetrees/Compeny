<?php

namespace App\Filament\Resources\Testimonials\Tables;

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

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                ImageColumn::make('avatar_path')
                    ->label('Avatar')
                    ->circular()
                    ->disk('public'),
                TextColumn::make('project.title')
                    ->label('Project')
                    ->icon('heroicon-m-rectangle-stack')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('author')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('role')
                    ->searchable()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('company')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
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
                SelectFilter::make('project')
                    ->relationship('project', 'title')
                    ->searchable()
                    ->preload(),
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
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->emptyStateHeading('No testimonials yet')
            ->emptyStateDescription('Collect client quotes to build trust on the site.')
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
