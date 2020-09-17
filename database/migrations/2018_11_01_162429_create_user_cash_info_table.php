<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCashInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_cash_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('bank_name',60)->default('');
            $table->string('bank_account',60)->default('');
            $table->string('real_name',60)->default('');
            $table->string('alipay_account',60)->default('');
            $table->string('wechat_nickname',60)->default('');
            $table->string('wechat_account',60)->default('');
            $table->integer('create_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_cash_info');
    }
}
