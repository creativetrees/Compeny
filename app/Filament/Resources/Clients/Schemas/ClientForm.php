<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client')
                    ->description('Logos shown in the "trusted by" strip.')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(120)
                            ->prefixIcon('heroicon-m-identification')
                            ->placeholder('Acme Inc.')
                            ->helperText('The client or brand name.'),
                        TextInput::make('website_url')
                            ->label('Website / link')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-link')
                            ->placeholder('https://example.com')
                            ->helperText('Where this logo links to when clicked on the site.'),
                        FileUpload::make('logo_path')
                            ->label('Logo image')
                            ->image()
                            ->imageEditor()
                            ->directory('clients')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Optional — leave empty to show the name as text only.'),
                        Toggle::make('is_featured')
                            ->label('Show in the marquee')
                            ->default(true)
                            ->helperText('Featured logos appear in the "trusted by" strip.'),
                    ]),
            ]);
    }
}
