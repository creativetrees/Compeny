<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Brand & Logo')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('brand_name')
                                    ->label('Brand / project name')
                                    ->required()
                                    ->default('Creative Trees Group')
                                    ->prefixIcon('heroicon-m-building-office-2')
                                    ->helperText('Full company name — used in the page title, OG tags, and footer.'),
                                TextInput::make('logo_text')
                                    ->label('Logo wordmark text')
                                    ->default('Creative Trees')
                                    ->prefixIcon('heroicon-m-pencil')
                                    ->helperText('Short text shown next to the logo in the header & footer. Leave empty to hide.'),
                                FileUpload::make('logo_path')
                                    ->label('Company logo')
                                    ->image()
                                    ->directory('site')
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/jpeg', 'image/webp'])
                                    ->helperText('Transparent PNG/SVG, max 2 MB. Empty = use the built-in mark.'),
                                FileUpload::make('favicon_path')
                                    ->label('Favicon')
                                    ->image()
                                    ->directory('site')
                                    ->maxSize(1024)
                                    ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                    ->helperText('Browser tab icon (ICO/PNG/SVG, e.g. 512×512), max 1 MB. Empty = default favicon.'),
                            ]),

                        Tab::make('Hero')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                TextInput::make('hero_eyebrow')->prefixIcon('heroicon-m-tag'),
                                Textarea::make('hero_title')
                                    ->helperText('Use a new line to split the headline into two lines.')
                                    ->columnSpanFull(),
                                Textarea::make('hero_subtitle')->columnSpanFull(),
                                TextInput::make('hero_cta_label')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                                TextInput::make('hero_cta_url')->url()->prefixIcon('heroicon-m-link'),
                            ]),

                        Tab::make('About')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Textarea::make('about_heading')->columnSpanFull(),
                                Textarea::make('about_body')->columnSpanFull()->rows(6),
                            ]),

                        Tab::make('Kontak')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                TextInput::make('contact_email')->email()->prefixIcon('heroicon-m-envelope'),
                                TextInput::make('contact_phone')->tel()->prefixIcon('heroicon-m-phone'),
                                TextInput::make('contact_address')->prefixIcon('heroicon-m-map-pin'),
                            ]),

                        Tab::make('Social & Stats')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Repeater::make('social_links')
                                    ->label('Social media')
                                    ->helperText('Pilih platform & isi URL profil. Tarik untuk mengurutkan.')
                                    ->schema([
                                        Select::make('platform')
                                            ->required()
                                            ->native(false)
                                            ->options([
                                                'X' => 'X (Twitter)', 'LinkedIn' => 'LinkedIn', 'GitHub' => 'GitHub',
                                                'Instagram' => 'Instagram', 'Dribbble' => 'Dribbble', 'Behance' => 'Behance',
                                                'YouTube' => 'YouTube', 'Facebook' => 'Facebook', 'TikTok' => 'TikTok',
                                                'Threads' => 'Threads', 'WhatsApp' => 'WhatsApp', 'Email' => 'Email',
                                            ]),
                                        TextInput::make('url')
                                            ->required()
                                            ->url()
                                            ->prefixIcon('heroicon-m-link')
                                            ->placeholder('https://...'),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                                    ->addActionLabel('Tambah sosial media')
                                    ->columnSpanFull(),
                                Repeater::make('stats')
                                    ->label('Stats (angka highlight)')
                                    ->schema([
                                        TextInput::make('label')->required(),
                                        TextInput::make('value')->required(),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->addActionLabel('Tambah stat')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                TextInput::make('seo_title')->prefixIcon('heroicon-m-hashtag'),
                                Textarea::make('seo_description')->columnSpanFull(),
                                FileUpload::make('seo_image_path')
                                    ->label('Social share image (OG)')
                                    ->image()
                                    ->directory('site')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp']),
                            ]),

                        Tab::make('Footer')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema([
                                TextInput::make('footer_tagline')->prefixIcon('heroicon-m-chat-bubble-bottom-center-text'),
                            ]),
                    ]),
            ]);
    }
}
