<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();      // heroicon name
            $table->string('summary');
            $table->text('description')->nullable();
            $table->json('capabilities')->nullable(); // list of bullet points
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['is_featured', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
