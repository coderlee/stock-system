<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',60)->default('');
            $table->string('get_address',60)->default('');
            $table->integer('sort')->default(0);
            $table->string('logo',60)->default('');
            $table->integer('create_time')->default(0);
            $table->tinyInteger('is_display')->default(0)->length(1);
            $table->decimal('min_number',23,8)->default(0)->comment('最小提币数量');
            $table->decimal('rate',10,2)->default(0)->comment('费率');
            $table->tinyInteger('is_lever')->default(0)->length(1)->comment('是否合约币 0否 1是');
            $table->tinyInteger('is_legal')->default(0)->length(1)->comment('是否法币 0否 1是');
            $table->tinyInteger('is_match')->default(0)->length(1)->comment('是否撮合交易 0否 1是');
            $table->tinyInteger('show_legal')->default(0)->length(1)->comment('是否显示法币商家 0否 1是');
            $table->string('type',20)->default('')->comment('基于哪个区块链');
            $table->integer('black_limt')->default(0)->comment('币种黑名单限制数量');
            $table->string('key')->default('');
            $table->string('contract_address')->default('');
            $table->string('total_account')->default('');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency');
    }
}
