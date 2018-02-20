<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedSpaceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = [
            'Residential living space, kitchen, bed room, study, bath room or toilet',
            'Residential individual: hall, corridor, staircase inside thermal envelope, attic inside thermal envelope',
            'Residential collective or non-residential: hall, corridor, staircase inside thermal envelope',
            'Thermally unconditioned adjacent space, such as storage room or unconditioned attic',
            'Thermally unconditioned sunspace or atrium',
            'Entrance hall/foyer',
            'Corridor',
            'Hall, corridor outside thermal envelope',
            'Office space',
            'Educational space',
            'Hospital bed room',
            'Hospital other room',
            'Hotels room',
            'Restaurant space',
            'Restaurant kitchen',
            'Meeting or seminar space',
            'Auditorium, lecture room',
            'Theatre or cinema space',
            'Server or computer room',
            'Sport facilities, thermally conditioned',
            'Sport facilities, thermally unconditioned',
            'Wholesale and retail trade services space (shop)',
            'Non-residential bath room, shower, toilet, if inside thermal envelope',
            'Spa area with sauna shower and/or relaxing area',
            'Space with indoor swimming pool',
            'Heated storage space',
            'Cooled storage space',
            'Non conditioned storage space',
            'Engine room',
            'Individual garage or collective indoor car park',
            'Barn',

        ];

        foreach ($types as $type) {
            DB::table('space_categories')->insert([
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
        Schema::table('space_categories', function (Blueprint $table) {
            //
        });
    }
}
