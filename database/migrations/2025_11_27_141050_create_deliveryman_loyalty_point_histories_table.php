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
        Schema::create('deliveryman_loyalty_point_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_man_id')->index();
            $table->uuid('transaction_id');
            $table->string('transaction_type');
            $table->string('point_conversion_type',20);
            $table->decimal('point',24,3)->default(0);
            $table->decimal('converted_amount',24,3)->default(0);
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveryman_loyalty_point_histories');
    }
};
