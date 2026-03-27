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
        Schema::table('vendors', function (Blueprint $table) {
             $table->renameColumn('bank_name', 'rejection_note');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->text('rejection_note')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('vendors', function (Blueprint $table) {
            $table->string('rejection_note', 255)->change();
            $table->renameColumn('rejection_note', 'bank_name');
        });
    }
};
