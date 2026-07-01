<?php

namespace App\Filament\Resources\StartSteps\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StartStepInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Start step')->icon('heroicon-m-list-bullet')->columns(2)->schema([
                TextEntry::make('title')->icon('heroicon-m-list-bullet')->weight('bold')->columnSpanFull(),
                TextEntry::make('description')->html()->prose()->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
