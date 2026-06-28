<?php

namespace App\Filament\Resources\PricingTiers\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PricingTierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Pricing tier')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Detail')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->prefixIcon('heroicon-m-identification'),
                                TextInput::make('term')
                                    ->required(),
                            ]),

                        Tab::make('Harga')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->prefixIcon('heroicon-m-banknotes'),
                                TextInput::make('price_label')
                                    ->default('From'),
                                TextInput::make('suffix'),
                            ]),

                        Tab::make('Konten')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Textarea::make('tagline')
                                    ->columnSpanFull(),
                                TagsInput::make('items')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Toggle::make('is_featured')
                                    ->required(),
                                TextInput::make('sort')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
