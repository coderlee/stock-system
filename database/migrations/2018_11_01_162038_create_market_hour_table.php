<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketHourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_hour', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('currency_id')->default(0);
            $table->integer('legal_id')->default(0);
            $table->decimal('start_price',20,5)->default(0);
            $table->decimal('end_price',20,5)->default(0);
            $table->decimal('highest',20,5)->default(0);
            $table->decimal('mminimum',20,5)->default(0);
            $table->integer('day_time')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->decimal('number',30,5)->default(0);
            $table->string('mar_id',100)->default('');
            $table->string('period',100)->default('');
            $table->tinyInteger('sign')->default(0);
            $table->index(['currency_id','legal_id','day_time','type','period']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_hour');
    }
}
