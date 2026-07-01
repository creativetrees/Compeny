<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail')->icon('heroicon-m-user-circle')->columns(2)->schema([
                TextEntry::make('project.title')->label('Project')->icon('heroicon-m-rectangle-stack')->placeholder('—'),
                TextEntry::make('author')->icon('heroicon-m-user')->weight('bold'),
                TextEntry::make('role')->icon('heroicon-m-briefcase')->placeholder('—'),
                TextEntry::make('company')->icon('heroicon-m-building-office-2')->placeholder('—'),
            ]),
            Section::make('Content')->icon('heroicon-m-chat-bubble-left-right')->columns(2)->schema([
                TextEntry::make('quote')->html()->prose()->columnSpanFull(),
                TextEntry::make('rating')->icon('heroicon-m-star')->placeholder('—'),
            ]),
            Section::make('Settings')->icon('heroicon-m-cog-6-tooth')->columns(2)->schema([
                ImageEntry::make('avatar_path')->label('Avatar')->circular()->disk('public')->placeholder('—'),
                IconEntry::make('is_featured')->label('Featured')->boolean(),
            ]),
        ]);
    }
}
