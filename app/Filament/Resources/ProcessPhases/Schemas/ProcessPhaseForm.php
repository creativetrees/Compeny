<?php

namespace App\Filament\Resources\ProcessPhases\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProcessPhaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fase proses')
                    ->icon('heroicon-o-arrow-path')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        Textarea::make('lead')
                            ->required(),
                        Textarea::make('body')
                            ->columnSpanFull(),
                        TagsInput::make('deliverables'),
                        TextInput::make('sort')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
