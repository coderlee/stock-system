<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminModuleActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_module_action', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_module_id')->default(0);
            $table->string('name',50)->default('');
            $table->string('action',50)->default('');
            $table->tinyInteger('level')->default(0)->length(4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_module_action');
    }
}
