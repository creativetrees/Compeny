<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
                            ->icon('heroicon-o-briefcase')
                            ->columns(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon('heroicon-m-rectangle-stack')
                                    ->placeholder('Select a category')
                                    ->helperText('Groups this project on the work page.'),
                                TextInput::make('title')
                                    ->required()
                                    ->prefixIcon('heroicon-m-briefcase')
                                    ->placeholder('Acme Rebrand')
                                    ->helperText('Shown as the heading on the case study.'),
                                TextInput::make('slug')
                                    ->required()
                                    ->prefixIcon('heroicon-m-hashtag')
                                    ->placeholder('acme-rebrand')
                                    ->helperText('URL segment — lowercase, no spaces.'),
                                TextInput::make('client_name')
                                    ->label('Client name')
                                    ->prefixIcon('heroicon-m-user')
                                    ->placeholder('Acme Inc.')
                                    ->helperText('The client this project was delivered for.'),
                                TextInput::make('year')
                                    ->numeric()
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->placeholder('2026')
                                    ->helperText('Year the project was completed.'),
                                TextInput::make('role')
                                    ->prefixIcon('heroicon-m-identification')
                                    ->placeholder('Brand & Web Design')
                                    ->helperText('Your studio’s role on the project.'),
                            ]),

                        Tab::make('Content')
                            ->icon('heroicon-o-document-text')
                            ->columns(2)
                            ->schema([
                                Textarea::make('summary')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('A short one-line pitch for this project.')
                                    ->helperText('Brief teaser shown on the work card.'),
                                Textarea::make('body')
                                    ->rows(6)
                                    ->columnSpanFull()
                                    ->placeholder('Tell the full story of the project…')
                                    ->helperText('Full case-study body shown on the project page.'),
                                TagsInput::make('services')
                                    ->columnSpanFull()
                                    ->placeholder('Add a service')
                                    ->helperText('Press Enter after each item.'),
                                TagsInput::make('results')
                                    ->columnSpanFull()
                                    ->placeholder('Add a result')
                                    ->helperText('Press Enter after each item.'),
                            ]),

                        Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->columns(2)
                            ->schema([
                                FileUpload::make('cover_path')
                                    ->label('Cover image')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('site/projects')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                                    ->columnSpanFull()
                                    ->helperText('Main image. PNG/JPG/WEBP, max 2 MB.'),
                                FileUpload::make('gallery')
                                    ->label('Gallery')
                                    ->image()
                                    ->imageEditor()
                                    ->multiple()
                                    ->reorderable()
                                    ->disk('public')
                                    ->directory('site/projects/gallery')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                                    ->columnSpanFull()
                                    ->helperText('Additional images. Drag to reorder. PNG/JPG/WEBP, max 2 MB each.'),
                                TextInput::make('website_url')
                                    ->label('Website URL')
                                    ->url()
                                    ->prefixIcon('heroicon-m-link')
                                    ->placeholder('https://example.com')
                                    ->helperText('Live link to the project, if public.'),
                            ]),

                        Tab::make('Settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->columns(2)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->helperText('Highlight this project in featured sections.'),
                                Select::make('status')
                                    ->required()
                                    ->native(false)
                                    ->default('published')
                                    ->options([
                                        'published' => 'Published',
                                        'draft' => 'Draft',
                                    ])
                                    ->prefixIcon('heroicon-m-flag')
                                    ->placeholder('Select a status')
                                    ->helperText('Only published projects appear on the site.'),
                            ]),
                    ]),
            ]);
    }
}
