<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Anggota tim')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->prefixIcon('heroicon-m-identification'),
                        TextInput::make('slug')
                            ->required()
                            ->prefixIcon('heroicon-m-hashtag'),
                        TextInput::make('role')
                            ->required(),
                        Textarea::make('bio')
                            ->columnSpanFull(),
                        TextInput::make('photo_path')
                            ->prefixIcon('heroicon-m-photo'),
                        TextInput::make('socials'),
                        Toggle::make('is_published')
                            ->required(),
                        TextInput::make('sort')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
