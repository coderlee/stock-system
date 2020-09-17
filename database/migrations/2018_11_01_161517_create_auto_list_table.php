<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('buy_user_id')->default(0)->comment('买方user_id');
            $table->integer('sell_user_id')->default(0)->comment('卖方user_id');
            $table->integer('currency_id')->default(0)->comment('币种id');
            $table->integer('legal_id')->default(0)->comment('法币id');
            $table->decimal('min_price',20,5)->default(0)->comment('最低');
            $table->decimal('max_price',20,5)->default(0)->comment('最高');
            $table->decimal('min_number',20,5)->default(0);
            $table->decimal('max_number',20,5)->default(0);
            $table->integer('need_second')->default(0);
            $table->integer('create_time')->default(0);
            $table->tinyInteger('is_start')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_list');
    }
}
