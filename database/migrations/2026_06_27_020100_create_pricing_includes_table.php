<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_includes', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('description');
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->index('sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_includes');
    }
};
