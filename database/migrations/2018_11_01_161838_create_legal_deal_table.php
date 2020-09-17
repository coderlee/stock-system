<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegalDealTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_deal', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('legal_deal_send_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->integer('seller_id')->default(0);
            $table->decimal('number', 20, 5)->default(0);
            $table->tinyInteger('is_sure')->default(0);
            $table->integer('create_time')->default(0);
            $table->integer('update_time')->default(0);
            $table->index(['legal_deal_send_id','seller_id']);

            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('legal_deal');
    }
}
