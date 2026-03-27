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
        Schema::create('subscription_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id');
            $table->foreignId('store_id');
            $table->foreignId('store_subscription_id')->nullable();
            $table->double('price', 24, 3)->default(0);
            $table->double('previous_due', 24, 3)->default(0);
            $table->integer('validity')->default(0);
            $table->string('payment_method', 191);
            $table->string('payment_status', 191);
            $table->string('reference', 191)->nullable();
            $table->double('paid_amount',24, 2);
            $table->integer('discount')->default(0);
            $table->json('package_details');
            $table->string('created_by', 50);
            $table->boolean('is_trial')->default(false);
            $table->boolean('transaction_status')->default(1);
            $table->enum('plan_type',['renew','new_plan','first_purchased','free_trial'])->default('first_purchased');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE subscription_transactions AUTO_INCREMENT = 1000000;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_transactions');
    }
};
