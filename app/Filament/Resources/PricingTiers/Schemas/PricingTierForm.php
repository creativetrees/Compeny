<?php

namespace App\Filament\Resources\PricingTiers\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PricingTierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('term')
                    ->required(),
                TextInput::make('price')
                    ->required(),
                TextInput::make('price_label')
                    ->default('From'),
                TextInput::make('suffix'),
                Textarea::make('tagline')
                    ->columnSpanFull(),
                TagsInput::make('items')
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
