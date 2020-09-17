<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersWalletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_wallet', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->integer('currency')->default(0);
            $table->string('address',50)->default('');
            $table->decimal('legal_balance',20,8)->default(0);
            $table->decimal('lock_legal_balance',20,8)->default(0);
            $table->decimal('change_balance',20,8)->default(0);
            $table->decimal('lock_change_balance',20,8)->default(0);
            $table->decimal('lever_balance',20,8)->default(0);
            $table->decimal('lock_lever_balance',20,8)->default(0);
            $table->integer('status')->default(0);
            $table->integer('create_time')->default(0);
            $table->decimal('old_balance',20,8)->default(0);
            $table->string('private')->default('');
            $table->index(['address','currency','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_wallet');
    }
}
