<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedMeasuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $measures = [
            'Vloerisolatie',
            'Gevelisolatie',
            'Dakisolatie',
            'Isolatieglas',
            'Kierdichting',
            'Ventilatie',
            'Cv-ketel',
            'Warmtepomp',
            'Biomassa',
            'Warmte afgifte',
            'Zonnepanelen',
            'Zonneboiler',
            'PVT',
            'Opslag',
            'Overig',


        ];

        foreach ($measures as $measure) {
            DB::table('measures')->insert([
                    ['name' => $measure],
                ]
            );
            /*DB::table('measures')->where('id', DB::raw("(select max(`id`)  from measures)"))->get();

            if (id ==1) {
                $measure_categories = [
                    'Isolatie van de vloer',
                    'Isolatie van de bodem',
                    'Isolatie van de kruipruimte',
                    'Leiding isolatie kruipruimte',
                ];
            }
            */

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('measures', function (Blueprint $table) {
            //
        });
    }
}
