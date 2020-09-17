<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersWalletOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_wallet_out', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->integer('currency')->default(0);
            $table->string('address',50)->default('');
            $table->decimal('number',20,8)->default(0);
            $table->integer('create_time')->default(0);
            $table->decimal('rate',13,2)->default(0);
            $table->tinyInteger('status')->default(0);
            $table->text('notes');
            $table->decimal('real_number',13,8)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_wallet_out');
    }
}
