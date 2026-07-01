<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Team member')->icon('heroicon-m-user')->columns(2)->schema([
                ImageEntry::make('photo_path')->label('Photo')->circular()->disk('public')->placeholder('—'),
                IconEntry::make('is_published')->label('Published')->boolean(),
                TextEntry::make('name')->icon('heroicon-m-user'),
                TextEntry::make('role')->icon('heroicon-m-briefcase'),
                TextEntry::make('slug')->icon('heroicon-m-link')->color('gray'),
                TextEntry::make('bio')->html()->prose()->columnSpanFull()->placeholder('—'),
                KeyValueEntry::make('socials')->columnSpanFull(),
            ]),
        ]);
    }
}
