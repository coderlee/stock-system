<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_number',30)->default('');
            $table->tinyInteger('type')->default(0);
            $table->string('phone',60)->default('');
            $table->string('email',60)->default('');
            $table->string('password')->default('');
            $table->string('pay_password')->default('');
            $table->integer('time')->default(0);
            $table->integer('parent_id')->default(0);
            $table->string('head_portrait',400)->default('');
            $table->string('extension_code',10)->default('');
            $table->integer('status')->default(0);
            $table->string('gesture_password')->default('');
            $table->string('is_auth')->default('');
            $table->string('nickname')->default('');
            $table->string('wallet_address')->default('');
            $table->tinyInteger('is_blacklist')->default(0);
            $table->index(['account_number','email','is_blacklist','phone','id','wallet_address'],'users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
