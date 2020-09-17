<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricalDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type',10)->default('');
            $table->integer('start_time')->default(0);
            $table->integer('end_time')->default(0);
            $table->string('data',500)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_data');
    }
}
