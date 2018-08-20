<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOfficeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->string('officer_designation',50)->after('identification_code');
            $table->string('address1',50)->after('officer_designation');
            $table->string('post_office',50)->after('address1');
            $table->string('pin',6)->after('post_office');

            $table->string('block_muni_id',9)->after('pin');
            $table->foreign('block_muni_id')->references('id')->on('block_munis');

            $table->string('police_station_id',9)->after('block_muni_id');
            $table->foreign('police_station_id')->references('id')->on('police_stations');

            $table->string('ac_id',3)->after('police_station_id');
            $table->foreign('ac_id')->references('id')->on('assembly_constituencies');

            $table->string('pc_id',3)->after('ac_id');
            $table->foreign('pc_id')->references('id')->on('parliamentary_constituencies');

            $table->string('district_id',5)->after('subdivision_id');
            $table->foreign('district_id')->references('id')->on('districts');

            $table->string('category_id',2)->after('district_id');
            $table->foreign('category_id')->references('id')->on('categories');

            $table->string('institute_id',2)->after('category_id');
            $table->foreign('institute_id')->references('id')->on('institutes');

            $table->string('email',50)->after('institute_id');
            $table->string('phone',15)->after('email');
            $table->string('mobile',10)->after('phone');
            $table->string('fax',15)->after('mobile');

            $table->smallInteger('total_staff')->after('fax');
            $table->smallInteger('male_staff')->after('total_staff');
            $table->smallInteger('female_staff')->after('male_staff');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            //
        });
    }
}
