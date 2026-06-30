<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Encrypted (AES-256 via APP_KEY) SMTP passwords for the CMS mail accounts,
            // keyed by email address. Kept OUT of the non-secret `emails` column and never
            // sent back to the browser. APP_KEY stays in .env, so the DB holds only ciphertext.
            $table->text('email_secrets')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('email_secrets');
        });
    }
};
