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
        Schema::table('module_zone', function (Blueprint $table) {
            $table->enum('delivery_charge_type', ['fixed', 'distance'])->default('distance');
            $table->double('fixed_shipping_charge', 23, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_zone', function (Blueprint $table) {
            $table->dropColumn('fixed_shipping_charge');
            $table->dropColumn('delivery_charge_type');
        });
    }
};
