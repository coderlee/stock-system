<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('name',60)->default('');
            $table->decimal('seller_balance',20,5)->default(0);
            $table->decimal('lock_seller_balance',20,5)->default(0);
            $table->string('wechat_nickname',60)->default('');
            $table->string('wechat_account',60)->default('');
            $table->string('ali_nickname',60)->default('');
            $table->string('ali_account',60)->default('');
            $table->integer('bank_id')->default(0);
            $table->string('bank_account',60)->default('');
            $table->string('bank_address',60)->default('');
            $table->integer('create_time')->default(0);
            $table->integer('currency_id')->default(0);
            $table->string('mobile',60)->default('');
            $table->index('user_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller');
    }
}
