<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionCompleteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_complete', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('way')->default(0);
            $table->integer('user_id')->default(0);
            $table->decimal('price',20,5)->default(0);
            $table->decimal('number',20,5)->default(0);
            $table->integer('create_time')->default(0);
            $table->integer('currency')->default(0);
            $table->integer('from_user_id')->default(0);
            $table->integer('legal')->default(0);
            $table->index(['create_time','currency','legal','number','price'],'complete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_complete');
    }
}
