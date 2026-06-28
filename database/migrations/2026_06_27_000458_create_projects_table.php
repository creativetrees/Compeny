<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client_name')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('role')->nullable();         // e.g. "Design & Engineering"
            $table->string('summary');
            $table->longText('body')->nullable();
            $table->string('cover_path')->nullable();
            $table->json('gallery')->nullable();        // list of image paths
            $table->json('services')->nullable();       // list of service labels
            $table->json('results')->nullable();        // [{label, value}]
            $table->string('website_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('published');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->index('category_id'); // Postgres does not auto-index FKs
            $table->index(['status', 'is_featured', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
