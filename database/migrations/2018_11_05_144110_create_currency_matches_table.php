<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_matches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('legal_id')->comment('法币id');
            $table->integer('currency_id')->comment('币种id');
            $table->tinyInteger('is_display')->comment('是否显示');
            $table->tinyInteger('market_from')->comment('0.无,1.交易所,2.火币接口');
            $table->tinyInteger('open_transaction')->comment('开启撮合交易');
            $table->tinyInteger('open_lever')->comment('开启合约交易');
            $table->integer('sort')->comment('排序');
            $table->integer('create_time')->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_matches');
    }
}
