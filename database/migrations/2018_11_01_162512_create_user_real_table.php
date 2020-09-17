<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRealTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_real', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('name',60)->default('');
            $table->string('card_id',60)->default('');
            $table->integer('create_time')->default(0);
            $table->tinyInteger('review_status')->default(0);
            $table->integer('review_time')->default(0);
            $table->string('front_pic')->default('');
            $table->string('reverse_pic')->default('');
            $table->string('hand_pic',60)->default('');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_real');
    }
}
