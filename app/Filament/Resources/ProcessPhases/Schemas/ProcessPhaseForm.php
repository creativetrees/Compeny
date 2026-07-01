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
                Section::make('Process phase')->columnSpanFull()
                    ->description('A single phase in the studio process, shown on the process page.')
                    ->icon('heroicon-m-rectangle-group')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->prefixIcon('heroicon-m-rectangle-group')
                            ->placeholder('Discovery')
                            ->helperText('The name of this phase.')
                            ->columnSpanFull(),
                        Textarea::make('lead')
                            ->required()
                            ->rows(2)
                            ->placeholder('Align on goals, constraints, and success metrics.')
                            ->helperText('One-line summary.')
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->rows(4)
                            ->placeholder('Describe what happens during this phase…')
                            ->helperText('Full description.')
                            ->columnSpanFull(),
                        TagsInput::make('deliverables')
                            ->placeholder('Add a deliverable')
                            ->helperText('Press Enter after each item.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
