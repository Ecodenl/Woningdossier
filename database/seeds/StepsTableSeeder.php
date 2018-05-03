<?php

use Illuminate\Database\Seeder;

class StepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $steps = [
            [
                'slug' => 'general-data',
                'name' => 'General data',
                'order' => 0,
            ],
            [
                'slug' => 'ventilation-information',
                'name' => 'Ventilation information',
                'order' => 1,
            ],
            [
                'slug' => 'wall-insulation',
                'name' => 'Wall Insulation',
                'order' => 2,
            ],
            [
                'slug' => 'insulated-glazing',
                'name' => 'Insulated Glazing',
                'order' => 3,
            ],
            [
                'slug' => 'floor-insulation',
                'name' => 'Floor Insulation',
                'order' => 4,
            ],
            [
                'slug' => 'roof-insulation',
                'name' => 'Roof Insulation',
                'order' => 5,
            ],
            [
                'slug' => 'high-efficiency-boiler',
                'name' => 'High Efficiency Boiler',
                'order' => 6,
            ],
            [
                'slug' => 'heat-pump',
                'name' => 'Heat Pump',
                'order' => 7,
            ],
            [
                'slug' => 'solar-panels',
                'name' => 'Solar Panels',
                'order' => 8,
            ],
            [
                'slug' => 'heater',
                'name' => 'Heater',
                'order' => 9,
            ],
        ];

        DB::table('steps')->insert($steps);
    }
}
