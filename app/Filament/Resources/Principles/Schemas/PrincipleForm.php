<?php

namespace App\Filament\Resources\Principles\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrincipleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description'),
                TextInput::make('sort')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
