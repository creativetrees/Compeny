<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service')->icon('heroicon-m-swatch')->columns(2)->schema([
                TextEntry::make('title')->icon('heroicon-m-swatch')->weight('bold'),
                TextEntry::make('slug')->icon('heroicon-m-link'),
                TextEntry::make('icon')->icon('heroicon-m-sparkles'),
                IconEntry::make('is_featured')->label('Featured')->boolean(),
                TextEntry::make('summary')->html()->columnSpanFull(),
                TextEntry::make('description')->html()->prose()->columnSpanFull(),
                TextEntry::make('capabilities')->badge()->columnSpanFull(),
            ]),
        ]);
    }
}
