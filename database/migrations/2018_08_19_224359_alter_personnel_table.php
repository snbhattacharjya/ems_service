<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPersonnelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->string('aadhaar',12)->nullable();
            $table->string('present_address',100);
            $table->string('permanent_address',100);
            $table->date('dob');
            $table->enum('gender',['M','F','O']);
            $table->string('scale',50);
            $table->mediumInteger('basic_pay');
            $table->smallInteger('grade_pay');
            $table->enum('emp_group',['A','B','C','D']);
            $table->enum('working_status',['Y','N']);
            $table->string('email',50);
            $table->string('phone',15)->nullable();
            $table->string('mobile',10);
            $table->string('qualification_id',2);
            $table->string('language_id',2);
            $table->string('epic',25);
            $table->tinyInteger('part_no')->nullable();
            $table->tinyInteger('sl_no')->nullable();
            $table->string('assembly_temp_id',3);
            $table->string('assembly_perm_id',3);
            $table->string('assembly_off_id',3);
            $table->string('block_muni_temp_id',9);
            $table->string('block_muni_perm_id',9);
            $table->string('block_muni_off_id',9);
            $table->string('district_id',5);
            $table->string('subdivision_id',7);
            $table->string('image_path')>nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personnel', function (Blueprint $table) {
            //
        });
    }
}
