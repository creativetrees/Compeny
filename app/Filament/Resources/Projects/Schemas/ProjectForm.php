<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
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
                TextInput::make('client_name'),
                TextInput::make('year')
                    ->numeric(),
                TextInput::make('role'),
                TextInput::make('summary')
                    ->required(),
                Textarea::make('body')
                    ->columnSpanFull(),
                TextInput::make('cover_path'),
                TextInput::make('gallery'),
                TextInput::make('services'),
                TextInput::make('results'),
                TextInput::make('website_url')
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
