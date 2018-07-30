<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssemblyConstituenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assembly_constituencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50)->unique();
            $table->integer('pc_id');
            $table->foreign('pc_id')->references('id')->on('parliamentary_constituencies');
            $table->integer('district_id');
            $table->foreign('district_id')->references('id')->on('districts');
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
        Schema::dropIfExists('assembly_constituencies');
    }
}
