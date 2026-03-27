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
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('item_id');
            $table->index('item_campaign_id');
            $table->index('user_id');
            $table->index('order_id');
            $table->index('store_id');
            $table->index('review_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('item_id');
            $table->dropIndex('item_campaim_id');
            $table->dropIndex('user_id');
            $table->dropIndex('order_id');
            $table->dropIndex('store_id');
            $table->dropIndex('review_id');
        });
    }
};
