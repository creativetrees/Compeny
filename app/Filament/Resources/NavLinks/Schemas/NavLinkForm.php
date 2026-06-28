<?php

namespace App\Filament\Resources\NavLinks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NavLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tautan navigasi')
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        Select::make('location')
                            ->required()
                            ->options([
                                'header' => 'Header',
                                'footer_studio' => 'Footer — Studio',
                                'footer_company' => 'Footer — Company',
                            ])
                            ->default('header'),
                        TextInput::make('label')
                            ->required()
                            ->prefixIcon('heroicon-m-tag'),
                        TextInput::make('url')
                            ->required()
                            ->default('/')
                            ->prefixIcon('heroicon-m-link'),
                        TextInput::make('sort')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
