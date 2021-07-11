<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetTransactionsIdEventPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('event_payment', function (Blueprint $table) {
            $table->unsignedBigInteger('target_transactions_id');

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
        Schema::table('event_payment', function (Blueprint $table) {
            $table->dropColumn('target_transactions_id');
        });
    }
}
