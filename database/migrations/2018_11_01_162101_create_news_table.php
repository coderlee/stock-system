<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('c_id')->default(0);
            $table->string('title',128)->default('');
            $table->tinyInteger('recommend')->default(0);
            $table->tinyInteger('audit')->default(0);
            $table->tinyInteger('display')->default(0);
            $table->tinyInteger('discuss')->default(0);
            $table->string('author',32)->default('');
            $table->tinyInteger('browse_grant')->default(0);
            $table->string('keyword')->default('');
            $table->string('abstract')->default('');
            $table->longText('content');
            $table->integer('views')->default(0);
            $table->integer('create_time')->default(0);
            $table->integer('update_time')->default(0);
            $table->string('thumbnail',500)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
