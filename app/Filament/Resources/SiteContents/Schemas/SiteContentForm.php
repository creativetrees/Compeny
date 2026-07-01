<?php

namespace App\Filament\Resources\SiteContents\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten')->columnSpanFull()
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        TextInput::make('group')
                            ->required()
                            ->default('General')
                            ->prefixIcon('heroicon-m-rectangle-group'),
                        TextInput::make('key')
                            ->required()
                            ->prefixIcon('heroicon-m-hashtag'),
                        TextInput::make('label')
                            ->required()
                            ->prefixIcon('heroicon-m-tag'),
                        Textarea::make('value')
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('sort')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
