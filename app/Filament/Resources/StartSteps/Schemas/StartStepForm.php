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
                Section::make('Start step')
                    ->description('A step in the "how to start" sequence shown on the start page.')
                    ->icon('heroicon-m-list-bullet')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')->required()
                            ->prefixIcon('heroicon-m-list-bullet')
                            ->placeholder('Send a brief')
                            ->helperText('Short, action-oriented name for the step.')
                            ->columnSpanFull(),
                        Textarea::make('description')->rows(4)->columnSpanFull()
                            ->placeholder('Tell us where you are headed and what success looks like…')
                            ->helperText('Explain what happens in this step.'),
                    ]),
            ]);
    }
}
