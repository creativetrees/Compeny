<?php

namespace App\Filament\Resources\Principles\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrincipleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Principle')->columnSpanFull()
                    ->description('An operating principle shown on the process page.')
                    ->icon('heroicon-m-light-bulb')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')->required()
                            ->prefixIcon('heroicon-m-light-bulb')
                            ->placeholder('Ship to learn')
                            ->helperText('Short, memorable name for the principle.')
                            ->columnSpanFull(),
                        RichEditor::make('description')->columnSpanFull()
                            ->placeholder('We release early and let real usage guide the next move…')
                            ->helperText('Explain what this principle means in practice.'),
                    ]),
            ]);
    }
}
