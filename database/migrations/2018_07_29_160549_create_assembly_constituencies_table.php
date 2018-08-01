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
            $table->mediumInteger('id')->unsigned()->primary();
            $table->string('name',50)->unique();
            $table->mediumInteger('subdivision_id')->unsigned();
            $table->foreign('subdivision_id')->references('id')->on('subdivisions');
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
