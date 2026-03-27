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
        Schema::create('parcel_cancellation_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->enum('user_type', ['customer', 'admin','deliveryman','vendor'])->default('customer');
            $table->enum('cancellation_type', ['before_pickup', 'after_pickup'])->default('before_pickup');
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_cancellation_reasons');
    }
};
