<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
                            ->columns(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon('heroicon-m-rectangle-stack')
                                    ->placeholder('Select a category')
                                    ->helperText('Groups this product on the public catalogue.'),
                                TextInput::make('title')
                                    ->required()
                                    ->prefixIcon('heroicon-m-cube')
                                    ->placeholder('Brand Identity Kit')
                                    ->helperText('Shown as the heading on the product card.'),
                                TextInput::make('slug')
                                    ->required()
                                    ->prefixIcon('heroicon-m-hashtag')
                                    ->placeholder('brand-identity-kit')
                                    ->helperText('URL segment — lowercase, no spaces.'),
                                TextInput::make('type')
                                    ->prefixIcon('heroicon-m-tag')
                                    ->placeholder('Package')
                                    ->helperText('Optional grouping label, e.g. Package or Add-on.'),
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
                                    ->helperText('Only published products appear on the site.'),
                            ]),

                        Tab::make('Content')
                            ->icon('heroicon-o-document-text')
                            ->columns(2)
                            ->schema([
                                RichEditor::make('summary')
                                    ->required()
                                    ->columnSpanFull()
                                    ->placeholder('A short one-line pitch for this product.')
                                    ->helperText('Brief teaser shown on the product card.'),
                                RichEditor::make('description')
                                    ->columnSpanFull()
                                    ->placeholder('Describe what is included and who it is for…')
                                    ->helperText('Full description shown on the product page.'),
                                TagsInput::make('features')
                                    ->columnSpanFull()
                                    ->placeholder('Add a feature')
                                    ->helperText('Press Enter after each item.'),
                                TextInput::make('price_label')
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->placeholder('From $2,500')
                                    ->helperText('Human-friendly price text shown to visitors.'),
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
                                    ->directory('site/products')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                                    ->columnSpanFull()
                                    ->helperText('Main image. PNG/JPG/WEBP, max 2 MB.'),
                            ]),

                        Tab::make('Links')
                            ->icon('heroicon-o-link')
                            ->columns(2)
                            ->schema([
                                TextInput::make('cta_label')
                                    ->label('Call-to-action label')
                                    ->prefixIcon('heroicon-m-cursor-arrow-rays')
                                    ->placeholder('Get started')
                                    ->helperText('Text shown on the action button.'),
                                TextInput::make('cta_url')
                                    ->label('Call-to-action URL')
                                    ->url()
                                    ->prefixIcon('heroicon-m-link')
                                    ->placeholder('https://example.com/contact')
                                    ->helperText('Where the action button links to.'),
                            ]),

                        Tab::make('Settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->columns(2)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->helperText('Highlight this product in featured sections.'),
                            ]),
                    ]),
            ]);
    }
}
