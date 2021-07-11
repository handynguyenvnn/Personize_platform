<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointColumnToWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('reports', function (Blueprint $table) {
        //     $table->string('events_time')->nullable();
        // });

        if (!Schema::hasColumn('withdraw_requests', 'point')) {
            Schema::table('withdraw_requests', function (Blueprint $table) {
                $table->float('point');
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
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropColumn('point');
        });
    }
}
