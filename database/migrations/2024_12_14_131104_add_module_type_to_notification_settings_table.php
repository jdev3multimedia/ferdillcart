<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->string('module_type',20)->default('all');
            DB::statement("ALTER TABLE `notification_settings` MODIFY `type` ENUM('admin', 'customer', 'store', 'deliveryman', 'provider') DEFAULT 'admin'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropColumn('module_type');
            DB::statement("ALTER TABLE `notification_settings` MODIFY `type` ENUM('admin', 'customer', 'store', 'deliveryman') DEFAULT 'admin'");

        });
    }
};
