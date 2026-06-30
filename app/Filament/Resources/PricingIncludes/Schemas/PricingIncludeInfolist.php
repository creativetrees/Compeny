<?php

namespace App\Filament\Resources\PricingIncludes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PricingIncludeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pricing include')->icon('heroicon-m-check-circle')->columns(2)->schema([
                TextEntry::make('label')->icon('heroicon-m-check-circle')->weight('bold')->columnSpanFull(),
                TextEntry::make('description')->prose()->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
