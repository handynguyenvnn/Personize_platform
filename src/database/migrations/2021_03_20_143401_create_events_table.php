<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('type')->nullable()->comment('1: multiguest; 2: live');
            $table->string('link_stream');
            $table->string('image');
            $table->time('time')->nullable();
            $table->date('date')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('category_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('status')->default(1)->comment('1: coming; 2: live; 3: finish; 4:cancel');
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
        Schema::dropIfExists('events');
    }
}
