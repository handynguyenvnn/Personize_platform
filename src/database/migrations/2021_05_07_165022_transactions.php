<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if(!Schema::hasTable('transactions'))
        {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                // $table->unsignedBigInteger('target_user_id')->nullable();
                $table->float('points'); 
                $table->string('type', 30)->index()->comment('1:deposit; 2:withdraw; 3:package; 4:pay; 5:earn; 6:refund; 7:joinevent, 8:adjustment, 9:gift'); // type can be anything in your app, by default we use "deposit", "withdraw", "package", "pay", "earn", "refund", "joinevent"
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
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
