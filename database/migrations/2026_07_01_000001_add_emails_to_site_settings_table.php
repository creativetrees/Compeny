<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Role-based mailbox directory, e.g. [{"role":"support","address":"support@…"}].
            // A dedicated json column (like social_links/stats) so removing a row persists —
            // page_content's recursive merge on save can't delete list items.
            $table->json('emails')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('emails');
        });
    }
};
