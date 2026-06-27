<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('type'),
                TextInput::make('summary')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price_label'),
                TextInput::make('features'),
                TextInput::make('cover_path'),
                TextInput::make('cta_label'),
                TextInput::make('cta_url')
                    ->url(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('published'),
                TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
