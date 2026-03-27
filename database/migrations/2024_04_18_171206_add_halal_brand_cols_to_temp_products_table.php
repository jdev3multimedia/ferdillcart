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
            $table->boolean('is_halal')->default(0);
            $table->boolean('brand_id')->default(0);
            $table->boolean('is_prescription_required')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_products', function (Blueprint $table) {
            $table->dropColumn('is_halal');
            $table->dropColumn('brand_id');
            $table->dropColumn('is_prescription_required');
        });
    }
};
