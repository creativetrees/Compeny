<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client')->icon('heroicon-m-building-office-2')->columns(2)->schema([
                ImageEntry::make('logo_path')->label('Logo')->disk('public')->placeholder('— text —'),
                IconEntry::make('is_featured')->label('Featured')->boolean(),
                TextEntry::make('name')->icon('heroicon-m-identification'),
                TextEntry::make('website_url')->label('Website / link')->icon('heroicon-m-link')
                    ->url(fn ($record) => $record->website_url)->openUrlInNewTab()->placeholder('—'),
            ]),
        ]);
    }
}
