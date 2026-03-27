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
        Schema::table('temp_products', function (Blueprint $table) {
            $table->string('nutrition_ids',255)->nullable();
            $table->string('allergy_ids',255)->nullable();
            $table->string('generic_ids',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_products', function (Blueprint $table) {
            $table->dropColumn('nutrition_ids');
            $table->dropColumn('allergy_ids');
            $table->dropColumn('generic_ids');
        });
    }
};
