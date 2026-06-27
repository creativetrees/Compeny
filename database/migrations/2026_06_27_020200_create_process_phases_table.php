<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_phases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('lead');
            $table->text('body');
            $table->json('deliverables')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_phases');
    }
};
