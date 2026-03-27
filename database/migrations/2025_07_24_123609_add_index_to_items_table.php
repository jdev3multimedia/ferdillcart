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
        Schema::table('items', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('store_id');
            $table->index('name');
            $table->index('slug');
            $table->index('price');
            $table->index('created_at');
            $table->index('order_count');
            $table->index('avg_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('category_id');
            $table->dropIndex('store_id');
            $table->dropIndex('name');
            $table->dropIndex('slug');
            $table->dropIndex('price');
            $table->dropIndex('created_at');
            $table->dropIndex('order_count');
            $table->dropIndex('avg_rating');
        });
    }
};
