<?php

namespace App\Filament\Resources\NavLinks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NavLinkInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Navigation link')->icon('heroicon-m-link')->columns(2)->schema([
                TextEntry::make('location')->badge(),
                TextEntry::make('label')->icon('heroicon-m-bookmark'),
                TextEntry::make('url')->icon('heroicon-m-link')->copyable()->columnSpanFull(),
            ]),
        ]);
    }
}
