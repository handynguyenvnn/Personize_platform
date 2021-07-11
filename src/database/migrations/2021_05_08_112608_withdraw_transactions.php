<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WithdrawTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('transactions_id');
            $table->unsignedInteger('withdraw_request_id')->index();
            $table->unsignedInteger('points');
            $table->float("transaction_fee"); //　fee that is charged for the withdrawal
            $table->float("transfer_fee"); // fee that is charged for bank transfers
            $table->float('amount_before_fees');
            $table->float("amount_after_fees"); 
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
        //
        Schema::dropIfExists('withdraw_transactions');

    }
}
