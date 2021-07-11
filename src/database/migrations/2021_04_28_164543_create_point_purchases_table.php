<?php
use App\Consts;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //amount
        Schema::create('point_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('transactions_id')->nullable();
            $table->unsignedBigInteger('package_id');
            $table->unsignedInteger('points');
            $table->float('value');
            $table->string('payment_type');
            $table->json('other_info')->nullable();
            $table->string('status', 30)->default(Consts::TRANSACTION_STATUS_CREATING)->index();
            $table->string('type', 30)->index()->comment('1: deposit');

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
        Schema::dropIfExists('point_purchases');
    }
}
