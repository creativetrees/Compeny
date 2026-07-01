<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail')->icon('heroicon-m-briefcase')->columns(2)->schema([
                TextEntry::make('category.name')->label('Category')->icon('heroicon-m-rectangle-stack')->placeholder('—'),
                TextEntry::make('title')->icon('heroicon-m-briefcase')->weight('bold'),
                TextEntry::make('slug')->icon('heroicon-m-hashtag')->copyable(),
                TextEntry::make('client_name')->label('Client name')->icon('heroicon-m-user')->placeholder('—'),
                TextEntry::make('year')->icon('heroicon-m-calendar')->placeholder('—'),
                TextEntry::make('role')->icon('heroicon-m-identification')->placeholder('—'),
            ]),

            Section::make('Content')->icon('heroicon-m-document-text')->columns(2)->schema([
                TextEntry::make('summary')->columnSpanFull()->placeholder('—'),
                TextEntry::make('body')->prose()->columnSpanFull()->placeholder('—'),
                TextEntry::make('services')->badge()->columnSpanFull()->placeholder('—'),
                // `results` is a list of {label, value} maps — flatten each to a string
                // so the badge can render it (echoing a raw array throws htmlspecialchars()).
                TextEntry::make('results')
                    ->badge()
                    ->getStateUsing(fn ($record): array => collect($record->results ?? [])
                        ->map(fn ($r) => is_array($r) ? trim(($r['label'] ?? '').': '.($r['value'] ?? ''), ' :') : $r)
                        ->filter()
                        ->values()
                        ->all())
                    ->columnSpanFull()
                    ->placeholder('—'),
            ]),

            Section::make('Media')->icon('heroicon-m-photo')->schema([
                ImageEntry::make('cover_path')->label('Cover image')->disk('public')->placeholder('—'),
                ImageEntry::make('gallery')->label('Gallery')->disk('public')->placeholder('—'),
                TextEntry::make('website_url')->label('Website URL')->icon('heroicon-m-link')->url(fn ($record) => $record->website_url, true)->copyable()->placeholder('—'),
            ]),

            Section::make('Settings')->icon('heroicon-m-cog-6-tooth')->columns(2)->schema([
                IconEntry::make('is_featured')->label('Featured')->boolean(),
                TextEntry::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'published' => 'success',
                    'draft' => 'gray',
                    default => 'gray',
                }),
            ]),
        ]);
    }
}
