<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            // Professional SEO + analytics.
            $table->text('seo_keywords')->nullable()->after('seo_description');           // comma-separated keywords
            $table->string('google_analytics_id')->nullable()->after('seo_image_path');    // GA4 measurement id, e.g. G-XXXXXXXXXX
            $table->boolean('seo_noindex')->default(false)->after('google_analytics_id');   // emit <meta robots="noindex">
        });

        // Footer tagline becomes a rich-text field (HTML can exceed 255 chars).
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->text('footer_tagline')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn(['seo_keywords', 'google_analytics_id', 'seo_noindex']);
        });

        // RichEditor HTML may exceed varchar(255), which Postgres would reject on the
        // type change. Destructively truncate first so the rollback can complete.
        DB::statement(
            'UPDATE site_settings SET footer_tagline = LEFT(footer_tagline, 255) WHERE LENGTH(footer_tagline) > 255'
        );

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('footer_tagline')->nullable()->change();
        });
    }
};
