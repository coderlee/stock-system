<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_day', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('currency_id')->default(0);
            $table->integer('legal_id')->default(0);
            $table->decimal('start_price',20,5)->default(0);
            $table->decimal('end_price',20,5)->default(0);
            $table->decimal('highest',20,5)->default(0);
            $table->decimal('mminimum',20,5)->default(0);
            $table->decimal('number',20,5)->default(0);
            $table->string('times',36)->default('');
            $table->string('mar_id',100)->default('');
            $table->integer('type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_day');
    }
}
