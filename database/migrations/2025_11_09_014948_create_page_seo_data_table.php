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
        Schema::create('page_seo_data', function (Blueprint $table) {
            $table->id();
            $table->string('page_name')->unique();
            $table->string('slug')->nullable();
            $table->string('title');
            $table->string('description');
            $table->string('image')->nullable();
            $table->json('meta_data')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_seo_data');
    }
};
