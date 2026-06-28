<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        TextInput::make('slug')
                            ->required()
                            ->prefixIcon('heroicon-m-hashtag'),
                        TextInput::make('type')
                            ->required()
                            ->default('project')
                            ->prefixIcon('heroicon-m-tag'),
                        TextInput::make('description'),
                        TextInput::make('sort')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
