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
            $table->string('brand_name')->default('Creative Trees Group');
            $table->string('hero_eyebrow')->nullable();
            $table->text('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_cta_label')->nullable();
            $table->string('hero_cta_url')->nullable();
            $table->text('about_heading')->nullable();
            $table->text('about_body')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address')->nullable();
            $table->json('social_links')->nullable();
            $table->json('stats')->nullable();          // [{label, value}]
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image_path')->nullable();
            $table->string('footer_tagline')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
