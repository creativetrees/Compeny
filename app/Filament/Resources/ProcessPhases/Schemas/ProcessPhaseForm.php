<?php

namespace App\Filament\Resources\ProcessPhases\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProcessPhaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('lead')
                    ->required(),
                Textarea::make('body')
                    ->columnSpanFull(),
                TagsInput::make('deliverables'),
                TextInput::make('sort')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
