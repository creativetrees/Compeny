<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'title'),
                TextInput::make('author')
                    ->required(),
                TextInput::make('role'),
                TextInput::make('company'),
                Textarea::make('quote')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('avatar_path'),
                TextInput::make('rating')
                    ->numeric(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
