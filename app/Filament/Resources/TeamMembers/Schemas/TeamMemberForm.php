<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('role')
                    ->required(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                TextInput::make('photo_path'),
                TextInput::make('socials'),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('sort')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
