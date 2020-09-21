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
            $table->decimal('amount', 20, 2);
            $table->unsignedBigInteger('user_id');
            $table->string('authorization_url')->nullable();
            $table->string('access_code')->nullable();
            $table->string('response_code')->nullable(); //00: Successful, anything else 'Not successful'
            $table->longText('response_description')->nullable(); //Payment Successful, Pending Payment, User Cancellation;
            $table->enum('payment_status', ['failed', 'successful', 'pending'])->default('pending'); //0 : failed; 1: Successful; 2: Pending;
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
