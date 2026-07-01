<?php

namespace App\Filament\Resources\PricingTiers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PricingTierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail')->icon('heroicon-m-banknotes')->columns(2)->schema([
                TextEntry::make('name')->icon('heroicon-m-banknotes')->weight('bold'),
                TextEntry::make('term')->icon('heroicon-m-clock')->placeholder('—'),
                TextEntry::make('tagline')->html()->prose()->columnSpanFull()->placeholder('—'),
            ]),
            Section::make('Pricing')->icon('heroicon-m-currency-dollar')->columns(3)->schema([
                TextEntry::make('price_label')->icon('heroicon-m-bookmark')->placeholder('—'),
                TextEntry::make('price')->icon('heroicon-m-banknotes'),
                TextEntry::make('suffix')->icon('heroicon-m-hashtag')->placeholder('—'),
            ]),
            Section::make('Content')->icon('heroicon-m-document-text')->schema([
                TextEntry::make('items')->badge()->placeholder('—'),
            ]),
            Section::make('Settings')->icon('heroicon-m-cog-6-tooth')->columns(2)->schema([
                IconEntry::make('is_featured')->label('Featured')->boolean(),
            ]),
        ]);
    }
}
