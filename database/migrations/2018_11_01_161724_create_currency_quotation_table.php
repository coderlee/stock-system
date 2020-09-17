<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyQuotationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_quotation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('match_id')->default(0)->comment('交易对id');
            $table->integer('legal_id')->default(0);
            $table->integer('currency_id')->default(0);
            $table->string('change')->default('')->comment('涨跌幅 带+ - 号');
            $table->decimal('volume',20,5)->default(0)->comment('成交量');
            $table->decimal('now_price',20,5)->default(0)->comment('当前价位');
            $table->integer('add_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_quotation');
    }
}
