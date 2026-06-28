<?php

namespace App\Filament\Resources\SiteContents\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('group')
                    ->required()
                    ->default('General'),
                TextInput::make('key')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                Textarea::make('value')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
