<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
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

class ProjectsTable
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
                TextColumn::make('client_name')
                    ->searchable(),
                TextColumn::make('year')
                    ->icon('heroicon-m-calendar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('role')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('website_url')
                    ->icon('heroicon-m-link')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-s-x-mark'),
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
                    ->options(Project::query()->whereNotNull('status')->distinct()->pluck('status', 'status')->all()),
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
            ->emptyStateIcon('heroicon-o-briefcase')
            ->emptyStateHeading('No projects yet')
            ->emptyStateDescription('Add a project to show it in your work showcase.')
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
