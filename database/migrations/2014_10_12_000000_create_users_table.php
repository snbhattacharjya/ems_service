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
            $table->string('name',50);
            $table->string('email',50)->unique();
            $table->string('mobile',10)->unique();
            $table->string('aadhaar',12)->unique();
            $table->string('designation',50);
            $table->tinyInteger('level');
            $table->foreign('level')->references('id')->on('user_levels');
            $table->string('area',50);
            $table->string('password');
            $table->boolean('is_active');
            $table->boolean('change_password');
            $table->rememberToken();
            $table->timestamps();
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
