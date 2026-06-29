<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // ── Brand / header identity (shared by header & footer) ──
            $table->string('brand_name')->default('Creative Trees Group');
            $table->string('logo_path')->nullable();        // uploaded company logo
            $table->string('logo_text')->nullable();        // optional wordmark override (empty = brand_name)
            $table->string('favicon_path')->nullable();     // uploaded favicon
            $table->text('header_description')->nullable();  // short line under the brand (mobile menu)
            $table->json('nav_menu')->nullable();           // header nav list [{label, url}]

            // ── Hero (home) ──
            $table->string('hero_eyebrow')->nullable();
            $table->text('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_cta_label')->nullable();
            $table->string('hero_cta_url')->nullable();
            $table->string('hero_cta_secondary_label')->nullable();
            $table->string('hero_cta_secondary_url')->nullable();

            // ── About ──
            $table->text('about_heading')->nullable();
            $table->text('about_body')->nullable();

            // ── Contact + social ──
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address')->nullable();
            $table->json('social_links')->nullable();
            $table->json('stats')->nullable();              // [{label, value}]

            // ── SEO + analytics ──
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->string('seo_image_path')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->boolean('seo_noindex')->default(false);

            // ── Footer ──
            $table->text('footer_tagline')->nullable();
            $table->string('footer_cta_eyebrow')->nullable();
            $table->text('footer_cta_title')->nullable();
            $table->text('footer_cta_body')->nullable();
            $table->string('footer_cta_label')->nullable();
            $table->string('footer_cta_url')->nullable();
            $table->string('footer_location')->nullable();
            $table->string('footer_copyright')->nullable();
            $table->string('footer_watermark')->nullable();

            // ── Per-page editable copy (resolved by the content() helper) ──
            // Nested by page, e.g. page_content['services']['hero_eyebrow'].
            $table->json('page_content')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
