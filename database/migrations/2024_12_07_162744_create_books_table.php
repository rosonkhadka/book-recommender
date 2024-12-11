<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('page_count')->nullable();
            $table->string('published_date')->nullable();
            $table->string('language')->nullable();
            $table->string('isbn')->nullable();
            $table->string('thumbnail_s')->nullable();
            $table->string('thumbnail_m')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
