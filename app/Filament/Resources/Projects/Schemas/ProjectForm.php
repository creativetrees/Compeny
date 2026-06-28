<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Project')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Detail')
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'name'),
                                TextInput::make('title')
                                    ->required()
                                    ->prefixIcon('heroicon-m-identification'),
                                TextInput::make('slug')
                                    ->required()
                                    ->prefixIcon('heroicon-m-hashtag'),
                                TextInput::make('client_name')
                                    ->prefixIcon('heroicon-m-user'),
                                TextInput::make('year')
                                    ->numeric()
                                    ->prefixIcon('heroicon-m-calendar'),
                                TextInput::make('role'),
                            ]),

                        Tab::make('Konten')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('summary')
                                    ->required(),
                                Textarea::make('body')
                                    ->columnSpanFull(),
                                TextInput::make('services'),
                                TextInput::make('results'),
                            ]),

                        Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                TextInput::make('cover_path')
                                    ->prefixIcon('heroicon-m-photo'),
                                TextInput::make('gallery'),
                                TextInput::make('website_url')
                                    ->url()
                                    ->prefixIcon('heroicon-m-link'),
                            ]),

                        Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Toggle::make('is_featured')
                                    ->required(),
                                TextInput::make('status')
                                    ->required()
                                    ->default('published')
                                    ->prefixIcon('heroicon-m-flag'),
                                TextInput::make('sort')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
