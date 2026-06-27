<?php

namespace App\Filament\Resources\NavLinks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NavLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('location')
                    ->required()
                    ->options([
                        'header' => 'Header',
                        'footer_studio' => 'Footer — Studio',
                        'footer_company' => 'Footer — Company',
                    ])
                    ->default('header'),
                TextInput::make('label')
                    ->required(),
                TextInput::make('url')
                    ->required()
                    ->default('/'),
                TextInput::make('sort')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
