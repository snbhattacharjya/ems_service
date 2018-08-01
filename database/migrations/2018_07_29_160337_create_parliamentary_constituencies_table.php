<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParliamentaryConstituenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parliamentary_constituencies', function (Blueprint $table) {
            $table->mediumInteger('id')->unsigned()->primary();
            $table->string('name',50)->unique();
            $table->mediumInteger('state_id')->unsigned();
            $table->foreign('state_id')->references('id')->on('states');
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
        Schema::dropIfExists('parliamentary_constituencies');
    }
}
