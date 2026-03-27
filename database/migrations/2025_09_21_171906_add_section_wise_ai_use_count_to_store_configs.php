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
          Schema::table( Schema::hasTable('store_configs')?  'store_configs' : 'storeConfigs', function (Blueprint $table) {
            $table->integer('section_wise_ai_use_count')->default(0);
            $table->integer('image_wise_ai_use_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table( Schema::hasTable('store_configs')?  'store_configs' : 'storeConfigs', function (Blueprint $table) {
            $table->dropColumn('section_wise_ai_use_count');
            $table->dropColumn('image_wise_ai_use_count');
        });
    }
};
