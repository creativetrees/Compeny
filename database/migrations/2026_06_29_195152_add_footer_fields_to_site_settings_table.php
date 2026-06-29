<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            // Footer CTA band ("Have something worth building?").
            $table->string('footer_cta_eyebrow')->nullable()->after('footer_tagline');
            $table->text('footer_cta_title')->nullable()->after('footer_cta_eyebrow');
            $table->text('footer_cta_body')->nullable()->after('footer_cta_title');
            $table->string('footer_cta_label')->nullable()->after('footer_cta_body');
            $table->string('footer_cta_url')->nullable()->after('footer_cta_label');

            // Footer baseline + watermark.
            $table->string('footer_location')->nullable()->after('footer_cta_url');    // e.g. "Jakarta · Remote-first"
            $table->string('footer_copyright')->nullable()->after('footer_location');  // override for "© YEAR Brand"
            $table->string('footer_watermark')->nullable()->after('footer_copyright'); // big wordmark text (default = brand)
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'footer_cta_eyebrow', 'footer_cta_title', 'footer_cta_body',
                'footer_cta_label', 'footer_cta_url',
                'footer_location', 'footer_copyright', 'footer_watermark',
            ]);
        });
    }
};
