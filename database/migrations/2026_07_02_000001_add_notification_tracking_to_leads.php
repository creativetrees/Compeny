<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // When the studio-notification email for this lead was successfully sent.
            // NULL after a failed/pending send — surfaced in the admin so a lead is
            // never silently lost when SMTP is down or misconfigured.
            $table->timestamp('notified_at')->nullable()->after('meta');
            $table->text('notification_error')->nullable()->after('notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['notified_at', 'notification_error']);
        });
    }
};
