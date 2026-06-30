<?php

namespace App\Filament\Resources\Principles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrincipleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Principle')->icon('heroicon-m-light-bulb')->columns(2)->schema([
                TextEntry::make('title')->icon('heroicon-m-light-bulb')->weight('bold')->columnSpanFull(),
                TextEntry::make('description')->prose()->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
