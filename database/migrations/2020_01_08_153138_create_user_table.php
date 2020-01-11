<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_class');
            $table->string('user_dormitory', 15);
            $table->string('user_name', 12);
            $table->enum('user_sex', ['男', '女'])->default('男');
            $table->bigInteger('user_phone');
            $table->enum('staying_status', ['是'])->nullable();
            $table->set('staying_time', ['周六', '周日'])->nullable();
            $table->string('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
