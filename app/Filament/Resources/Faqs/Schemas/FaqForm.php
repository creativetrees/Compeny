<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('FAQ')
                ->description('A single question and answer shown on the pricing page FAQ.')
                ->icon('heroicon-m-question-mark-circle')
                ->columns(2)
                ->schema([
                    TextInput::make('question')->required()
                        ->prefixIcon('heroicon-m-question-mark-circle')
                        ->placeholder('How long does a project take?')
                        ->helperText('The question as a visitor would phrase it.')
                        ->columnSpanFull(),
                    Textarea::make('answer')->required()->rows(4)->columnSpanFull()
                        ->placeholder('Most engagements run 4–8 weeks…')
                        ->helperText('A clear, direct answer. Plain text.'),
                    Toggle::make('is_published')->default(true)->inline(false)
                        ->helperText('Only published FAQs appear on the public site.'),
                ]),
        ]);
    }
}
