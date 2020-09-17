<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robot', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('currency_id')->default(0);
            $table->integer('legal_id')->default(0);
            $table->integer('buy_user_id')->default(0);
            $table->integer('sell_user_id')->default(0);
            $table->integer('create_time')->default(0);
            $table->integer('status')->default(0);
            $table->integer('second')->default(0);
            $table->integer('sell')->default(0);
            $table->integer('buy')->default(0);
            $table->decimal('number_max',20,5)->default(0);
            $table->decimal('number_min',20,5)->default(0);
            $table->decimal('float_number_down',20,5)->default(0);
            $table->decimal('float_number_up',20,5)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('robot');
    }
}
