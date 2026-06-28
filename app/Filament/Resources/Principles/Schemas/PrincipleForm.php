<?php

namespace App\Filament\Resources\Principles\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrincipleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Prinsip')
                    ->icon('heroicon-o-light-bulb')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        Textarea::make('description'),
                        TextInput::make('sort')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
