<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail')->icon('heroicon-m-cube')->columns(2)->schema([
                TextEntry::make('category.name')->label('Category')->icon('heroicon-m-rectangle-stack')->placeholder('—'),
                TextEntry::make('title')->icon('heroicon-m-cube')->weight('bold'),
                TextEntry::make('slug')->icon('heroicon-m-hashtag')->copyable(),
                TextEntry::make('type')->icon('heroicon-m-tag')->placeholder('—'),
                TextEntry::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'published' => 'success',
                    'draft' => 'gray',
                    default => 'gray',
                }),
            ]),

            Section::make('Content')->icon('heroicon-m-document-text')->columns(2)->schema([
                TextEntry::make('summary')->html()->columnSpanFull()->placeholder('—'),
                TextEntry::make('description')->html()->prose()->columnSpanFull()->placeholder('—'),
                TextEntry::make('features')->badge()->columnSpanFull()->placeholder('—'),
                TextEntry::make('price_label')->icon('heroicon-m-banknotes')->placeholder('—'),
            ]),

            Section::make('Media')->icon('heroicon-m-photo')->schema([
                ImageEntry::make('cover_path')->label('Cover image')->disk('public')->placeholder('—'),
            ]),

            Section::make('Links')->icon('heroicon-m-link')->columns(2)->schema([
                TextEntry::make('cta_label')->label('Call-to-action label')->icon('heroicon-m-cursor-arrow-rays')->placeholder('—'),
                TextEntry::make('cta_url')->label('Call-to-action URL')->icon('heroicon-m-link')->url(fn ($record) => $record->cta_url, true)->copyable()->placeholder('—'),
            ]),

            Section::make('Settings')->icon('heroicon-m-cog-6-tooth')->columns(2)->schema([
                IconEntry::make('is_featured')->label('Featured')->boolean(),
            ]),
        ]);
    }
}
