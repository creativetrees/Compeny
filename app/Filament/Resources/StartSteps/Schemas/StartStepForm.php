<?php

namespace App\Filament\Resources\StartSteps\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StartStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Langkah')
                    ->icon('heroicon-o-flag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        TextInput::make('sort')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
