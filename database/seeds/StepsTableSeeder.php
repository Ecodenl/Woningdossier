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
                'short' => 'general-data',
                'names' => [
                    'en' => 'General data',
                    'nl' => 'Algemene gegevens',
                ],
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Gebouwkenmerken',
                ],
                'slug' => 'gebouw-kenmerken',
                'short' => 'building-characteristics',
                'parent_short' => 'general-data',
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Huidige staat',
                ],
                'slug' => 'huidige-staat',
                'short' => 'current-state',
                'parent_short' => 'general-data',
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Gebruik',
                ],
                'slug' => 'gebruik',
                'short' => 'usage',
                'parent_short' => 'general-data',
                'order' => 2,
            ],
            [
                'names' => [
                    'nl' => 'Interesse',
                ],
                'slug' => 'interesse',
                'short' => 'interest',
                'parent_short' => 'general-data',
                'order' => 3,
            ],
            [
                'slug' => 'ventilation',
                'short' => 'ventilation',
                'names' => [
                    'en' => 'Ventilation',
                    'nl' => 'Ventilatie',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'wall-insulation',
                'short' => 'wall-insulation',
                'names' => [
                    'en' => 'Wall Insulation',
                    'nl' => 'Gevelisolatie',
                ],
                'order' => 3,
            ],
            [
                'slug' => 'insulated-glazing',
                'short' => 'insulated-glazing',
                'names' => [
                    'en' => 'Insulated Glazing',
                    'nl' => 'Isolerende beglazing',
                ],
                'order' => 4,
            ],
            [
                'slug' => 'floor-insulation',
                'short' => 'floor-insulation',
                'names' => [
                    'en' => 'Floor Insulation',
                    'nl' => 'Vloerisolatie',
                ],
                'order' => 5,
            ],
            [
                'slug' => 'roof-insulation',
                'short' => 'roof-insulation',
                'names' => [
                    'en' => 'Roof Insulation',
                    'nl' => 'Dakisolatie',
                ],
                'order' => 6,
            ],
            [
                'slug' => 'high-efficiency-boiler',
                'short' => 'high-efficiency-boiler',
                'names' => [
                    'en' => 'High Efficiency Boiler',
                    'nl' => 'HR CV-ketel',
                ],
                'order' => 7,
            ],
            [
                'slug' => 'heat-pump',
                'short' => 'heat-pump',
                'names' => [
                    'en' => 'Heat Pump',
                    'nl' => 'Warmtepomp',
                ],
                'order' => 8,
            ],
            [
                'slug' => 'solar-panels',
                'short' => 'solar-panels',
                'names' => [
                    'en' => 'Solar Panels',
                    'nl' => 'Zonnepanelen',
                ],
                'order' => 9,
            ],
            [
                'slug' => 'heater',
                'short' => 'heater',
                'names' => [
                    'en' => 'Heater',
                    'nl' => 'Zonneboiler',
                ],
                'order' => 10,
            ],
            // the steps used for the "quick scan"
            [
                'slug' => 'woninggegevens',
                'short' => 'building-data',
                'name' => [
                    'en' => 'Building data',
                    'nl' => 'Woninggegevens',
                ],
                'order' => 1,
            ],
            [
                'slug' => 'bewoners-gebruik',
                'short' => 'usage-quick-scan',
                'name' => [
                    'en' => 'Usage',
                    'nl' => 'Gebruik',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'woonwensen',
                'short' => 'living-requirements',
                'name' => [
                    'en' => 'Living requirements',
                    'nl' => 'Woonwensen',
                ],
                'order' => 3,
            ],
            [
                'slug' => 'woonstatus',
                'short' => 'residential-status',
                'name' => [
                    'en' => 'Residential status',
                    'nl' => 'Woonstatus',
                ],
                'order' => 4,
            ],
        ];

        foreach ($steps as $step) {

            if (! DB::table('steps')->where('slug', $step['slug'])->first() instanceof stdClass) {
                $insertStepData = [
                    'slug' => $step['slug'],
                    'short' => $step['short'],
                    'name' => json_encode($step['name']),
                    'order' => $step['order'],
                ];

                \DB::table('steps')->updateOrInsert(['short' => $step['short']], $insertStepData);
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
