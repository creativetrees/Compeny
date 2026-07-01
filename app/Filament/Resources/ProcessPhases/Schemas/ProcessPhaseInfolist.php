<?php

namespace App\Filament\Resources\ProcessPhases\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProcessPhaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Process phase')->icon('heroicon-m-rectangle-group')->columns(2)->schema([
                TextEntry::make('name')->icon('heroicon-m-rectangle-group')->weight('bold'),
                TextEntry::make('lead'),
                TextEntry::make('body')->html()->prose()->columnSpanFull(),
                TextEntry::make('deliverables')->badge()->columnSpanFull(),
            ]),
        ]);
    }
}
