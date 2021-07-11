<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->nullable();
            $table->string('email', 100)->index()->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->unsignedTinyInteger('role')->comment('1: admin; 2:business')->nullable()->index();
            $table->string('provider_id')->comment('')->nullable()->index();
            $table->string('provider')->nullable();
            $table->text('avatar')->nullable();
            $table->bigInteger('balance')->default(0);
            $table->unsignedTinyInteger('age')->nullable();
            $table->unsignedTinyInteger('sex')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
