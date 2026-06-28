<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Testimonial')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Detail')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Select::make('project_id')
                                    ->relationship('project', 'title'),
                                TextInput::make('author')
                                    ->required()
                                    ->prefixIcon('heroicon-m-identification'),
                                TextInput::make('role'),
                                TextInput::make('company')
                                    ->prefixIcon('heroicon-m-building-office-2'),
                            ]),

                        Tab::make('Konten')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Textarea::make('quote')
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('avatar_path')
                                    ->prefixIcon('heroicon-m-photo'),
                            ]),

                        Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                TextInput::make('rating')
                                    ->numeric()
                                    ->prefixIcon('heroicon-m-star'),
                                Toggle::make('is_featured')
                                    ->required(),
                                TextInput::make('sort')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }
}
