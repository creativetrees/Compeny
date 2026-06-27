<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand_name')
                    ->required()
                    ->default('Creative Trees Group'),
                TextInput::make('hero_eyebrow'),
                Textarea::make('hero_title')
                    ->columnSpanFull(),
                Textarea::make('hero_subtitle')
                    ->columnSpanFull(),
                TextInput::make('hero_cta_label'),
                TextInput::make('hero_cta_url')
                    ->url(),
                Textarea::make('about_heading')
                    ->columnSpanFull(),
                Textarea::make('about_body')
                    ->columnSpanFull(),
                TextInput::make('contact_email')
                    ->email(),
                TextInput::make('contact_phone')
                    ->tel(),
                TextInput::make('contact_address'),
                TextInput::make('social_links'),
                TextInput::make('stats'),
                TextInput::make('seo_title'),
                Textarea::make('seo_description')
                    ->columnSpanFull(),
                FileUpload::make('seo_image_path')
                    ->image(),
                TextInput::make('footer_tagline'),
            ]);
    }
}
