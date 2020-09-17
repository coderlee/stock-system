<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeverTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lever_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->decimal('price',20,5)->default(0);
            $table->decimal('number',20,5)->default(0);
            $table->integer('create_time')->default(0);
            $table->integer('currency')->default(0)->length(6);
            $table->integer('legal')->default(0)->length(6);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('multiple')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lever_transaction');
    }
}
