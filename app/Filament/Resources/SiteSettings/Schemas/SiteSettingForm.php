<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
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
                KeyValue::make('social_links')
                    ->keyLabel('Platform')
                    ->valueLabel('URL')
                    ->helperText('e.g. X → https://x.com/…  ·  LinkedIn → https://linkedin.com/…')
                    ->columnSpanFull(),
                KeyValue::make('stats')
                    ->keyLabel('Label')
                    ->valueLabel('Value')
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
