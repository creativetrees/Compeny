<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Layanan')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        TextInput::make('slug')
                            ->required()
                            ->prefixIcon('heroicon-m-hashtag'),
                        TextInput::make('icon'),
                        TextInput::make('summary')
                            ->required(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        TextInput::make('capabilities'),
                        Toggle::make('is_featured')
                            ->required(),
                        TextInput::make('sort')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
