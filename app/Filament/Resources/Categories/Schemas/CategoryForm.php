<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category')
                    ->description('A grouping used to organise projects and products on the public site.')
                    ->icon('heroicon-m-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required()
                            ->prefixIcon('heroicon-m-tag')
                            ->placeholder('Branding')
                            ->helperText('Display name shown to visitors.'),
                        TextInput::make('slug')->required()
                            ->prefixIcon('heroicon-m-link')
                            ->placeholder('branding')
                            ->helperText('URL segment — lowercase, no spaces.'),
                        TextInput::make('type')->required()->default('project')
                            ->prefixIcon('heroicon-m-rectangle-stack')
                            ->placeholder('project')
                            ->helperText('Groups projects/products.'),
                        Textarea::make('description')->rows(4)->columnSpanFull()
                            ->placeholder('Identity, logos, and visual systems…')
                            ->helperText('Optional short summary of what this category covers.'),
                    ]),
            ]);
    }
}
