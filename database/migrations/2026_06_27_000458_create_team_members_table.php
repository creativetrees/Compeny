<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('role');
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->json('socials')->nullable();        // {linkedin, x, github, dribbble}
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
