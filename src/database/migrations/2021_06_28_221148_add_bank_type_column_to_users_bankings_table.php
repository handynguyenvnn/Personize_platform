<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankTypeColumnToUsersBankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_bankings', function (Blueprint $table) {
            $table->string('bank_account_type')->after('bank_account_holder')->default('普通');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_bankings', function (Blueprint $table) {
            $table->dropColumn('bank_account_type');
        });
    }
}
