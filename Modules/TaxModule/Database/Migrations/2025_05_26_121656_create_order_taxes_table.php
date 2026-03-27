<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tax_name');
            $table->string('tax_type');
            $table->string('tax_on');
            $table->double('tax_rate', 23, 8)->default(0);
            $table->double('tax_amount', 23, 8)->default(0);
            $table->double('before_tax_amount', 23, 8)->default(0);
            $table->double('after_tax_amount', 23, 8)->default(0);
            $table->string('tax_payer')->nullable();
            $table->string('country_code',20)->nullable()->index();
            $table->foreignId('order_id')->nullable();
            $table->string('order_type')->nullable();
            $table->integer('quantity')->default(1)->nullable();
            $table->foreignId('tax_id');
            $table->foreignId('taxable_id')->nullable();
            $table->string('taxable_type')->nullable();
            $table->foreignId('store_id')->nullable();
            $table->string('store_type')->nullable();
            $table->foreignId('system_tax_setup_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_taxes');
    }
}
