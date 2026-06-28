<?php

namespace App\Filament\Resources\PricingIncludes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PricingIncludeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Termasuk')
                    ->icon('heroicon-o-check-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->required()
                            ->prefixIcon('heroicon-m-tag'),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        TextInput::make('sort')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
