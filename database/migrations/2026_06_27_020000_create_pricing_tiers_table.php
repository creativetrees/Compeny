<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('term');
            $table->string('price_label')->default('From');
            $table->string('price');
            $table->string('suffix')->nullable();
            $table->text('tagline');
            $table->json('items');
            $table->boolean('is_featured')->default(false);
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->index(['is_featured', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_tiers');
    }
};
