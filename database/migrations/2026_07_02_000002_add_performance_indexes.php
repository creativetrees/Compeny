<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dashboard widgets filter leads by status AND a created_at range on every
        // poll; a composite (status, created_at) serves that in one index scan
        // instead of intersecting two single-column indexes.
        Schema::table('leads', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'leads_status_created_at_index');
        });

        // Public pages run Project::published()->ordered() (WHERE status ORDER BY sort);
        // the existing (status, is_featured, sort) index can't satisfy the sort when
        // is_featured is skipped, so add (status, sort).
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['status', 'sort'], 'projects_status_sort_index');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_status_created_at_index');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_status_sort_index');
        });
    }
};
