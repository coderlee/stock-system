<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',60)->default('');
            $table->string('symbol',60)->default('');
            $table->string('website_slug',60)->default('');
            $table->integer('rank')->default(0);
            $table->unsignedBigInteger('circulating_supply')->default(0);
            $table->unsignedBigInteger('total_supply')->default(0);
            $table->unsignedBigInteger('max_supply')->default(0);
            $table->text('quotes');
            $table->unsignedInteger('last_updated')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market');
    }
}
