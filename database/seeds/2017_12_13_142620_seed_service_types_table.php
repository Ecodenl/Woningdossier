<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            'Heating',
            'Cooling',
            'Ventilation',
            'Humidification',
            'Dehumidification',
            'Domestic hot water',
            'Lighting',
            'External lighting',
            'Building automation and control',
            'People transport',
            'PV-wind',
            'appliances',
            'Others',

        ];

        foreach ($types as $type) {
            DB::table('service_types')->insert([
                    ['name' => $type],
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
