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
                Section::make('Navigation link')
                    ->description('A single link shown in the header or footer navigation.')
                    ->icon('heroicon-m-link')
                    ->columns(2)
                    ->schema([
                        Select::make('location')
                            ->required()
                            ->native(false)
                            ->default('header')
                            ->prefixIcon('heroicon-m-map-pin')
                            ->options([
                                'header' => 'Header',
                                'footer_studio' => 'Footer · Studio',
                                'footer_company' => 'Footer · Company',
                            ])
                            ->helperText('Where this link appears.'),
                        TextInput::make('label')
                            ->required()
                            ->prefixIcon('heroicon-m-bookmark')
                            ->placeholder('Work')
                            ->helperText('The text shown for this link.'),
                        TextInput::make('url')
                            ->required()
                            ->prefixIcon('heroicon-m-link')
                            ->placeholder('/work')
                            ->helperText('Path or full URL.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
