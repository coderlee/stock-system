<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegalDealSendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_deal_send', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id')->default(0);
            $table->integer('currency_id')->default(0);
            $table->enum('type',['buy','sell'])->default('sell');
            $table->enum('way',['bank','we_chat','ali_pay'])->default('bank');
            $table->decimal('price',20,5)->default(0);
            $table->decimal('total_number',20,5)->default(0);
            $table->decimal('surplus_number',20,5)->default(0);
            $table->decimal('min_number',20,5)->default(0);
            $table->tinyInteger('is_done')->default(0);
            $table->integer('create_time')->default(0);
            $table->index(['seller_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('legal_deal_send');
    }
}
