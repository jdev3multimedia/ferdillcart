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
        Schema::create('surge_prices', function (Blueprint $table) {
            $table->id();
            $table->string('surge_price_name');
            $table->text('customer_note')->nullable();
            $table->boolean('customer_note_status')->default(true);
            $table->json('module_ids')->nullable();
            $table->foreignId('zone_id');
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('price_type', ['amount', 'percent'])->default('amount');
            $table->boolean('status')->default(true);
            $table->boolean('is_permanent')->default(false);
            $table->enum('duration_type', ['daily', 'weekly', 'custom'])->default('daily');
            $table->json('weekly_days')->nullable();
            $table->json('custom_days')->nullable();
            $table->json('custom_times')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surge_prices');
    }
};
