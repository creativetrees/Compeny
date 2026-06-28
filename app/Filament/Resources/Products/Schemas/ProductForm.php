<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Detail')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'name'),
                                TextInput::make('title')
                                    ->required()
                                    ->prefixIcon('heroicon-m-identification'),
                                TextInput::make('slug')
                                    ->required()
                                    ->prefixIcon('heroicon-m-hashtag'),
                                TextInput::make('type')
                                    ->prefixIcon('heroicon-m-tag'),
                                TextInput::make('price_label')
                                    ->prefixIcon('heroicon-m-banknotes'),
                            ]),

                        Tab::make('Konten')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('summary')
                                    ->required(),
                                Textarea::make('description')
                                    ->columnSpanFull(),
                                TextInput::make('features'),
                            ]),

                        Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                TextInput::make('cover_path')
                                    ->prefixIcon('heroicon-m-photo'),
                            ]),

                        Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                TextInput::make('cta_label')
                                    ->prefixIcon('heroicon-m-cursor-arrow-rays'),
                                TextInput::make('cta_url')
                                    ->url()
                                    ->prefixIcon('heroicon-m-link'),
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
