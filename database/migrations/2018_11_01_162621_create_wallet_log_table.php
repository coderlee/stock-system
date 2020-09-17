<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_log_id')->default(0);
            $table->integer('wallet_id')->default(0);
            $table->integer('balance_type')->default(0);
            $table->integer('lock_type')->default(0);
            $table->decimal('before',20,8)->default(0);
            $table->decimal('change',20,8)->default(0);
            $table->decimal('after',20,8)->default(0);
            $table->string('memo')->default('');
            $table->integer('create_time')->default(0);
            $table->index(['account_log_id','balance_type','create_time','lock_type','wallet_id'],'wallet_log');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_log');
    }
}
