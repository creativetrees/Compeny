<?php

namespace App\Filament\Resources\PricingTiers\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PricingTierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Pricing tier')->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Detail')->icon('heroicon-o-rectangle-stack')->columns(2)->schema([
                    TextInput::make('name')->required()->prefixIcon('heroicon-m-banknotes')
                        ->placeholder('Sprint')->helperText('Name of the engagement tier.'),
                    TextInput::make('term')->required()->prefixIcon('heroicon-m-clock')
                        ->placeholder('2–4 weeks')->helperText('Typical duration or commitment.'),
                    Textarea::make('tagline')->rows(2)->columnSpanFull()
                        ->placeholder('A focused burst to ship one thing well.')
                        ->helperText('One-line summary shown under the tier name.'),
                ]),
                Tab::make('Pricing')->icon('heroicon-o-banknotes')->columns(2)->schema([
                    TextInput::make('price_label')->default('From')->prefixIcon('heroicon-m-bookmark')
                        ->placeholder('From')->helperText('Prefix before the price (e.g. "From", "Starting at").'),
                    TextInput::make('price')->required()->prefixIcon('heroicon-m-banknotes')
                        ->placeholder('$8k')->helperText('The headline figure shown on the card.'),
                    TextInput::make('suffix')->prefixIcon('heroicon-m-hashtag')
                        ->placeholder('/ project')->helperText('Text after the price (e.g. "/ month").'),
                ]),
                Tab::make('Content')->icon('heroicon-o-document-text')->columns(2)->schema([
                    TagsInput::make('items')->columnSpanFull()->placeholder('Add a feature')
                        ->helperText('One feature per line. Press Enter after each item.'),
                ]),
                Tab::make('Settings')->icon('heroicon-o-cog-6-tooth')->columns(2)->schema([
                    Toggle::make('is_featured')->label('Featured')->inline(false)
                        ->helperText('Highlight this tier as "Most popular" on the pricing page.'),
                ]),
            ]),
        ]);
    }
}
