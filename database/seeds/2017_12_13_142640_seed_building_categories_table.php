<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedBuildingCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $category = [
            'Single-family houses of different types',
            'Apartment blocks',
            'Homes for elderly and disabled people',
            'Residence for collective use',
            'Mobile home',
            'Holiday home',
            'Offices',
            'Educational buildings',
            'Hospitals',
            'Hotels and restaurants',
            'Sports facilities',
            'Wholesale and retail trade services buildings',
            'ata centre',
            'Industrial sites',
            'Workshops',


        ];

        foreach ($types as $type) {
            DB::table('building_cateegories')->insert([
                    ['name' => $category],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_types', function (Blueprint $table) {
            //
        });
    }
}
