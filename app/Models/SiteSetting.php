<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    protected static ?self $cached = null;

    protected $fillable = [
        'brand_name', 'logo_path', 'logo_text', 'favicon_path', 'nav_menu',
        'hero_eyebrow', 'hero_title', 'hero_subtitle',
        'hero_cta_label', 'hero_cta_url', 'hero_cta_secondary_label', 'hero_cta_secondary_url',
        'about_heading', 'about_body',
        'contact_email', 'contact_phone', 'contact_address', 'social_links', 'emails', 'email_secrets',
        'stats', 'seo_title', 'seo_description', 'seo_keywords', 'seo_image_path',
        'google_analytics_id', 'seo_noindex', 'footer_tagline',
        'footer_cta_eyebrow', 'footer_cta_title', 'footer_cta_body', 'footer_cta_label', 'footer_cta_url',
        'footer_location', 'footer_copyright', 'footer_watermark', 'page_content',
    ];

    protected function casts(): array
    {
        return [
            'nav_menu' => 'array',
            'social_links' => 'array',
            'emails' => 'array',
            'email_secrets' => 'encrypted:array',
            'stats' => 'array',
            'seo_noindex' => 'boolean',
            'page_content' => 'array',
        ];
    }

    /** Public URL for the uploaded logo (or null). */
    public function getLogoUrlAttribute(): ?string
    {
        return static::mediaUrl($this->logo_path);
    }

    /** Public URL for the uploaded favicon (or null). */
    public function getFaviconUrlAttribute(): ?string
    {
        return static::mediaUrl($this->favicon_path);
    }

    /** Public URL for the uploaded social/OG image (or null). */
    public function getSeoImageUrlAttribute(): ?string
    {
        return static::mediaUrl($this->seo_image_path);
    }

    protected static function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, 'http') ? $path : Storage::url($path);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::$cached = null);
    }

    /**
     * The single settings row (id = 1), memoised per request so the global
     * view composer doesn't fire one query per rendered Blade component.
     */
    public static function current(): self
    {
        if (static::$cached !== null) {
            return static::$cached;
        }

        try {
            return static::$cached = static::query()->firstOrCreate(['id' => 1]);
        } catch (\Throwable $e) {
            // Surface the failure (pending migration / DB outage) instead of silently
            // serving a blank model, then cache the fallback so a sustained outage
            // doesn't re-query on every rendered component.
            report($e);

            return static::$cached = new self;
        }
    }
}
