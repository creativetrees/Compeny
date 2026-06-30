<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Team member')
                ->description('A person shown on the public team page.')
                ->icon('heroicon-m-user')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()
                        ->prefixIcon('heroicon-m-user')
                        ->placeholder('Jane Doe')
                        ->helperText('Full name shown on the team page.'),
                    TextInput::make('slug')->required()
                        ->prefixIcon('heroicon-m-link')
                        ->placeholder('jane-doe')
                        ->helperText('URL segment — lowercase, no spaces.'),
                    TextInput::make('role')->required()
                        ->prefixIcon('heroicon-m-briefcase')
                        ->placeholder('Creative Director')
                        ->helperText('Job title or role within the studio.'),
                    Textarea::make('bio')->rows(4)->columnSpanFull()
                        ->placeholder('A short paragraph about this person…')
                        ->helperText('Optional — a brief biography.'),
                    FileUpload::make('photo_path')->label('Photo')
                        ->avatar()->imageEditor()->disk('public')->directory('site/team')
                        ->visibility('public')->maxSize(2048)
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                        ->helperText('Square photo, max 2 MB.'),
                    KeyValue::make('socials')->columnSpanFull()
                        ->keyLabel('Platform')->valueLabel('URL')
                        ->helperText('e.g. LinkedIn → https://…'),
                    Toggle::make('is_published')->inline(false)
                        ->helperText('Only published members appear on the public site.'),
                ]),
        ]);
    }
}
