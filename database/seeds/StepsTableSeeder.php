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
        $steps = [
            [
                'slug' => 'general-data',
                'names' => [
                    'en' => 'General data',
                    'nl' => 'Algemene gegevens',
                ],
                'order' => 0,
            ],
            [
                'slug' => 'ventilation-information',
                'names' => [
                    'en' => 'Ventilation information',
                    'nl' => 'Ventilatie informatie',
                ],
                'order' => 1,
            ],
            [
                'slug' => 'wall-insulation',
                'names' => [
                    'en' => 'Wall Insulation',
                    'nl' => 'Gevelisolatie',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'insulated-glazing',
                'names' => [
                    'en' => 'Insulated Glazing',
                    'nl' => 'Isolerende beglazing',
                ],
                'order' => 3,
            ],
            [
                'slug' => 'floor-insulation',
                'names' => [
                    'en' => 'Floor Insulation',
                    'nl' => 'Vloerisolatie',
                ],
                'order' => 4,
            ],
            [
                'slug' => 'roof-insulation',
                'names' => [
                    'en' => 'Roof Insulation',
                    'nl' => 'Dakisolatie',
                ],
                'order' => 5,
            ],
            [
                'slug' => 'high-efficiency-boiler',
                'names' => [
                    'en' => 'High Efficiency Boiler',
                    'nl' => 'HR CV-ketel',
                ],
                'order' => 6,
            ],
            [
                'slug' => 'heat-pump',
                'names' => [
                    'en' => 'Heat Pump',
                    'nl' => 'Warmtepomp',
                ],
                'order' => 7,
            ],
            [
                'slug' => 'solar-panels',
                'names' => [
                    'en' => 'Solar Panels',
                    'nl' => 'Zonnepanelen',
                ],
                'order' => 8,
            ],
            [
                'slug' => 'heater',
                'names' => [
                    'en' => 'Heater',
                    'nl' => 'Zonneboiler',
                ],
                'order' => 9,
            ],
        ];

        foreach ($steps as $step) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($step['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('steps')->insert([
                'slug' => $step['slug'],
                'name' => $uuid,
                'order' => $step['order'],
            ]);
        }

        $allCooperations = \App\Models\Cooperation::all();

        $steps = \App\Models\Step::all();

        foreach ($allCooperations as $cooperation) {
            foreach ($steps as $step) {
                $cooperation->steps()->attach($step);
                $cooperation->steps()->updateExistingPivot($step->id, ['order' => $step->order]);
            }
        }
    }
}
