<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('FAQ')->icon('heroicon-m-question-mark-circle')->columns(2)->schema([
                TextEntry::make('question')->icon('heroicon-m-question-mark-circle')->weight('bold')->columnSpanFull(),
                TextEntry::make('answer')->prose()->columnSpanFull(),
                IconEntry::make('is_published')->label('Published')->boolean(),
            ]),
        ]);
    }
}
