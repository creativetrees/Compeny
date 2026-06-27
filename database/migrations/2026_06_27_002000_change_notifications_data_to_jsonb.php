<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Filament's database notifications query `data->>'format'`, which in PostgreSQL
 * requires the `data` column to be json/jsonb (the `->>` operator is undefined
 * for `text`). The default Laravel notifications migration creates it as `text`,
 * so on existing databases we convert it in place.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING data::jsonb');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::text');
        }
    }
};
