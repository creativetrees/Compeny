<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service')
                    ->description('A single discipline shown on the services page.')
                    ->icon('heroicon-m-swatch')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->prefixIcon('heroicon-m-swatch')
                            ->placeholder('UX & UI Design')
                            ->helperText('The name of this service.'),
                        TextInput::make('slug')
                            ->required()
                            ->prefixIcon('heroicon-m-link')
                            ->placeholder('ux-ui-design')
                            ->helperText('URL segment — lowercase, no spaces.'),
                        TextInput::make('icon')
                            ->prefixIcon('heroicon-m-sparkles')
                            ->placeholder('heroicon-o-cube')
                            ->helperText('Heroicon name, e.g. heroicon-o-cube.'),
                        Toggle::make('is_featured')
                            ->inline(false)
                            ->default(false)
                            ->helperText('Highlight this service on the public page.'),
                        Textarea::make('summary')
                            ->required()
                            ->rows(2)
                            ->placeholder('Interfaces that are clear, fast, and on-brand.')
                            ->helperText('Short tagline.')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(4)
                            ->placeholder('Describe what this service covers…')
                            ->helperText('Full description of the service.')
                            ->columnSpanFull(),
                        TagsInput::make('capabilities')
                            ->placeholder('Add a capability')
                            ->helperText('Press Enter after each item.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
