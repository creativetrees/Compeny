<?php

namespace App\Filament\Resources\PricingIncludes\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PricingIncludeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pricing include')->columnSpanFull()
                    ->description('An item in the "what is always included" list on the pricing page.')
                    ->icon('heroicon-m-check-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')->required()
                            ->prefixIcon('heroicon-m-check-circle')
                            ->placeholder('Dedicated senior team')
                            ->helperText('Short name of what is included.')
                            ->columnSpanFull(),
                        RichEditor::make('description')->columnSpanFull()
                            ->placeholder('No account layers — the people who scope your work also do it…')
                            ->helperText('Optional detail explaining the inclusion.'),
                    ]),
            ]);
    }
}
