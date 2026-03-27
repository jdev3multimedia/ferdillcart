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
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->text('identity_image')->change();
            $table->double('loyalty_point',23, 8)->default(0)->nullable();
            $table->string('ref_code')->nullable();
            $table->foreignId('ref_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->string('identity_image')->change();
            $table->dropColumn('loyalty_point');
            $table->dropColumn('ref_code');
            $table->dropColumn('ref_by');
        });
    }
};
