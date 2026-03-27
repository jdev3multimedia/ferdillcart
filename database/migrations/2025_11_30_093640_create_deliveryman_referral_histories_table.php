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
        Schema::create('deliveryman_referral_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_man_id')->index();
            $table->foreignId('referrer_id')->index()->nullable();
            $table->uuid('transaction_id')->unique();
            $table->string('refer_type',20)->default('referral');
            $table->decimal('amount',24,3)->default(0);
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveryman_referral_histories');
    }
};
