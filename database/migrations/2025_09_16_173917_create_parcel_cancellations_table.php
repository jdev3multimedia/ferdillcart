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
        Schema::create('parcel_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index();
            $table->text('reason')->nullable();
            $table->string('cancel_by')->nullable();
            $table->text('note')->nullable();
            $table->string('return_otp')->nullable();
            $table->double('return_fee')->default(0)->nullable();
            $table->string('return_fee_payment_status')->default('unpaid')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->double('dm_penalty_fee')->default(0)->nullable();
            $table->boolean('before_pickup')->default(1);
            $table->boolean('set_return_date')->default(0);
            $table->boolean('is_delivery_charge_refundable')->default(0);
            $table->boolean('is_refunded')->default(0);
            $table->double('refund_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_cancellations');
    }
};
