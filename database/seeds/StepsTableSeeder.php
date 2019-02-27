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
                'slug' => 'building-detail',
                'names' => [
                    'en' => 'Building details',
                    'nl' => 'Woning details',
                ],
                'order' => 0,
            ],
            [
                'slug' => 'general-data',
                'names' => [
                    'en' => 'General data',
                    'nl' => 'Algemene gegevens',
                ],
                'order' => 1,
            ],
            [
                'slug' => 'ventilation-information',
                'names' => [
                    'en' => 'Ventilation information',
                    'nl' => 'Ventilatie informatie',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'wall-insulation',
                'names' => [
                    'en' => 'Wall Insulation',
                    'nl' => 'Gevelisolatie',
                ],
                'order' => 3,
            ],
            [
                'slug' => 'insulated-glazing',
                'names' => [
                    'en' => 'Insulated Glazing',
                    'nl' => 'Isolerende beglazing',
                ],
                'order' => 4,
            ],
            [
                'slug' => 'floor-insulation',
                'names' => [
                    'en' => 'Floor Insulation',
                    'nl' => 'Vloerisolatie',
                ],
                'order' => 5,
            ],
            [
                'slug' => 'roof-insulation',
                'names' => [
                    'en' => 'Roof Insulation',
                    'nl' => 'Dakisolatie',
                ],
                'order' => 6,
            ],
            [
                'slug' => 'high-efficiency-boiler',
                'names' => [
                    'en' => 'High Efficiency Boiler',
                    'nl' => 'HR CV-ketel',
                ],
                'order' => 7,
            ],
            [
                'slug' => 'heat-pump',
                'names' => [
                    'en' => 'Heat Pump',
                    'nl' => 'Warmtepomp',
                ],
                'order' => 8,
            ],
            [
                'slug' => 'solar-panels',
                'names' => [
                    'en' => 'Solar Panels',
                    'nl' => 'Zonnepanelen',
                ],
                'order' => 9,
            ],
            [
                'slug' => 'heater',
                'names' => [
                    'en' => 'Heater',
                    'nl' => 'Zonneboiler',
                ],
                'order' => 10,
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

            if (! DB::table('steps')->where('slug', $step['slug'])->first() instanceof stdClass) {
                \DB::table('steps')->insert([
                    'slug' => $step['slug'],
                    'name' => $uuid,
                    'order' => $step['order'],
                ]);
            }
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
