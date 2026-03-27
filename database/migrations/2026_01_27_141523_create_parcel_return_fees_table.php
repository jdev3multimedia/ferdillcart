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
        Schema::create('parcel_return_fees', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->index()->nullable();
            $table->foreignId('delivery_man_id')->index()->nullable();
            $table->foreignId('user_id')->index();
            $table->foreignId('order_id')->index();
            $table->decimal('amount', 10, 5)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_return_fees');
    }
};
