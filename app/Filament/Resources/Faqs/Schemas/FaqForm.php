<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FAQ')
                    ->icon('heroicon-o-question-mark-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('question')
                            ->required()
                            ->prefixIcon('heroicon-m-question-mark-circle'),
                        Textarea::make('answer')
                            ->required()
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->default(true),
                        TextInput::make('sort')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
