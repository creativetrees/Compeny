<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Brand & identity ──
                TextInput::make('brand_name')
                    ->label('Brand / project name')
                    ->required()
                    ->default('Creative Trees Group')
                    ->helperText('Full company name — used in the page title, OG tags, and footer.'),
                TextInput::make('logo_text')
                    ->label('Logo wordmark text')
                    ->default('Creative Trees')
                    ->helperText('Short text shown next to the logo in the header & footer. Leave empty to hide.'),
                FileUpload::make('logo_path')
                    ->label('Company logo')
                    ->image()
                    ->directory('site')
                    ->imageEditor()
                    ->helperText('Transparent PNG/SVG. Empty = use the built-in mark.'),
                FileUpload::make('favicon_path')
                    ->label('Favicon')
                    ->image()
                    ->directory('site')
                    ->helperText('Browser tab icon (ICO/PNG/SVG, e.g. 512×512). Empty = default favicon.'),

                // ── Hero ──
                TextInput::make('hero_eyebrow'),
                Textarea::make('hero_title')
                    ->helperText('Use a new line to split the headline into two lines.')
                    ->columnSpanFull(),
                Textarea::make('hero_subtitle')
                    ->columnSpanFull(),
                TextInput::make('hero_cta_label'),
                TextInput::make('hero_cta_url')
                    ->url(),

                // ── About ──
                Textarea::make('about_heading')
                    ->columnSpanFull(),
                Textarea::make('about_body')
                    ->columnSpanFull(),

                // ── Contact ──
                TextInput::make('contact_email')
                    ->email(),
                TextInput::make('contact_phone')
                    ->tel(),
                TextInput::make('contact_address'),

                // ── Social & stats ──
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

                // ── SEO ──
                TextInput::make('seo_title'),
                Textarea::make('seo_description')
                    ->columnSpanFull(),
                FileUpload::make('seo_image_path')
                    ->label('Social share image (OG)')
                    ->image()
                    ->directory('site'),

                // ── Footer ──
                TextInput::make('footer_tagline'),
            ]);
    }
}
