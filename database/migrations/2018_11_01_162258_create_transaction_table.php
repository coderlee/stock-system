<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_user_id')->default(0);
            $table->integer('currency')->default(0);
            $table->integer('to_user_id')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->decimal('number',20,5)->default(0);
            $table->string('remarks',500)->default('');
            $table->integer('time')->default(0);
            $table->integer('status')->default(0);
            $table->decimal('price',20,5)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
}
