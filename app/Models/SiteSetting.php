<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected static ?self $cached = null;

    protected $fillable = [
        'brand_name', 'hero_eyebrow', 'hero_title', 'hero_subtitle',
        'hero_cta_label', 'hero_cta_url', 'about_heading', 'about_body',
        'contact_email', 'contact_phone', 'contact_address', 'social_links',
        'stats', 'seo_title', 'seo_description', 'seo_image_path', 'footer_tagline',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'stats' => 'array',
        ];
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
        } catch (\Throwable) {
            return new self;
        }
    }
}
