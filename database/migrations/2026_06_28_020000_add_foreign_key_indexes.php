<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Postgres does NOT auto-index foreign-key columns (unlike MySQL). Index the FK
 * columns used in joins (->with('category')) and cascade lookups so they don't
 * sequential-scan as data grows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->index('category_id');
        });
        Schema::table('testimonials', function (Blueprint $table) {
            $table->index('project_id');
        });
        Schema::table('nav_links', function (Blueprint $table) {
            $table->index(['location', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['category_id']);
        });
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
        });
        Schema::table('nav_links', function (Blueprint $table) {
            $table->dropIndex(['location', 'sort']);
        });
    }
};
