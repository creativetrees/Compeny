<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->nullable();        // SaaS | Template | Service
            $table->string('summary');
            $table->text('description')->nullable();
            $table->string('price_label')->nullable();  // e.g. "From $2,000"
            $table->json('features')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('published');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->index('category_id'); // Postgres does not auto-index FKs
            $table->index(['status', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
