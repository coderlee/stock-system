<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_chat', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_user_id')->default(0);
            $table->integer('to_user_id')->default(0);
            $table->string('content',500)->default('');
            $table->tinyInteger('offline')->default(0);
            $table->string('type',60)->default('');
            $table->integer('add_time')->default(0);
            $table->index('from_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_chat');
    }
}
