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
        Schema::table('order_details', function (Blueprint $table) {
           $table->foreignId('category_id')->nullable();
           $table->string('discount_on_product_by')->nullable();
           $table->string('tax_status')->nullable();
           $table->double('discount_percentage',23, 8)->default(0)->nullable();
           $table->double('addon_discount',23, 8)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->dropColumn('discount_on_product_by');
            $table->dropColumn('tax_status');
            $table->dropColumn('discount_percentage');
            $table->dropColumn('addon_discount');
        });
    }
};
