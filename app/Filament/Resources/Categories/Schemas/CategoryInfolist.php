<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')->icon('heroicon-m-tag')->columns(2)->schema([
                TextEntry::make('name')->icon('heroicon-m-tag')->weight('bold'),
                TextEntry::make('slug')->icon('heroicon-m-link')->color('gray'),
                TextEntry::make('type')->icon('heroicon-m-rectangle-stack')->badge(),
                TextEntry::make('description')->html()->prose()->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
