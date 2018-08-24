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
            $table->string('aadhaar',12)->nullable()->after('designation');
            $table->string('present_address',100)->after('aadhaar');
            $table->string('permanent_address',100)->after('present_address');
            $table->date('dob')->after('permanent_address');
            $table->enum('gender',['M','F','O'])->after('dob');
            $table->string('scale',50)->after('gender');
            $table->mediumInteger('basic_pay')->after('scale');
            $table->smallInteger('grade_pay')->after('basic_pay');
            $table->enum('emp_group',['A','B','C','D'])->after('grade_pay');
            $table->enum('working_status',['Y','N'])->after('emp_group');
            $table->string('email',50)->after('working_status');
            $table->string('phone',15)->nullable()->after('email');
            $table->string('mobile',10)->after('phone');

            $table->string('qualification_id',2)->after('mobile');
            $table->foreign('qualification_id')->references('id')->on('qualifications');

            $table->tinyInteger('language_id')->unsigned()->after('qualification_id');
            $table->foreign('language_id')->references('id')->on('languages');

            $table->string('epic',25)->after('language_id');
            $table->tinyInteger('part_no')->nullable()->after('epic');
            $table->tinyInteger('sl_no')->nullable()->after('part_no');

            $table->string('assembly_temp_id',3)->after('sl_no');
            $table->foreign('assembly_temp_id')->references('id')->on('assembly_constituencies');

            $table->string('assembly_perm_id',3)->after('assembly_temp_id');
            $table->foreign('assembly_perm_id')->references('id')->on('assembly_constituencies');

            $table->string('assembly_off_id',3)->after('assembly_perm_id');
            $table->foreign('assembly_off_id')->references('id')->on('assembly_constituencies');

            $table->string('block_muni_temp_id',9)->after('assembly_off_id');
            $table->foreign('block_muni_temp_id')->references('id')->on('block_munis');

            $table->string('block_muni_perm_id',9)->after('block_muni_temp_id');
            $table->foreign('block_muni_perm_id')->references('id')->on('block_munis');

            $table->string('block_muni_off_id',9)->after('block_muni_perm_id');
            $table->foreign('block_muni_off_id')->references('id')->on('block_munis');

            $table->string('district_id',5)->after('block_muni_off_id');
            $table->foreign('district_id')->references('id')->on('districts');

            $table->string('subdivision_id',7)->after('district_id');
            $table->foreign('subdivision_id')->references('id')->on('subdivisions');

            $table->string('branch_ifsc',11)->after('subdivision_id');
            $table->string('bank_account_no',15)->after('branch_ifsc');

            $table->string('image_path')->nullable()->after('subdivision_id');

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
