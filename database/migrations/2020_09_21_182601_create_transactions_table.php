<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('reference', 20);
            $table->decimal('amount', 20, 2)->default('0.00');
            $table->unsignedBigInteger('user_id');
            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('meter_number')->nullable();
            $table->string('smartcard_number')->nullable();
            $table->string('addon_months_paid_for')->nullable();
            $table->string('product_months_paid_for')->nullable();
            $table->string('addon_code')->nullable();
            $table->string('product_code')->nullable();
            $table->string('address')->nullable();
            $table->string('service_type')->nullable();
            $table->string('outstanding_balance')->nullable();
            $table->string('token_code')->nullable();
            $table->string('token_amount')->nullable();
            $table->string('amount_of_power')->nullable();
            $table->string('transaction_code')->nullable();
            $table->string('transaction_description')->nullable();
            $table->enum('transaction_status', ['failed', 'successful', 'pending'])->default('pending');
            $table->longText('raw_output')->nullable();
            $table->longText('response_full')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
