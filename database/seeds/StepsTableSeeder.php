<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => [
                    'en' => 'General data',
                    'nl' => 'Algemene gegevens',
                ],
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Gebouwkenmerken',
                ],
                'slug' => 'gebouw-kenmerken',
                'short' => 'building-characteristics',
                'parent_short' => 'general-data',
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Huidige staat',
                ],
                'slug' => 'huidige-staat',
                'short' => 'current-state',
                'parent_short' => 'general-data',
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Gebruik',
                ],
                'slug' => 'gebruik',
                'short' => 'usage',
                'parent_short' => 'general-data',
                'order' => 2,
            ],
            [
                'name' => [
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
                'name' => [
                    'en' => 'Ventilation',
                    'nl' => 'Ventilatie',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'wall-insulation',
                'short' => 'wall-insulation',
                'name' => [
                    'en' => 'Wall Insulation',
                    'nl' => 'Gevelisolatie',
                ],
                'order' => 3,
            ],
            [
                'slug' => 'insulated-glazing',
                'short' => 'insulated-glazing',
                'name' => [
                    'en' => 'Insulated Glazing',
                    'nl' => 'Isolerende beglazing',
                ],
                'order' => 4,
            ],
            [
                'slug' => 'floor-insulation',
                'short' => 'floor-insulation',
                'name' => [
                    'en' => 'Floor Insulation',
                    'nl' => 'Vloerisolatie',
                ],
                'order' => 5,
            ],
            [
                'slug' => 'roof-insulation',
                'short' => 'roof-insulation',
                'name' => [
                    'en' => 'Roof Insulation',
                    'nl' => 'Dakisolatie',
                ],
                'order' => 6,
            ],
            [
                'slug' => 'high-efficiency-boiler',
                'short' => 'high-efficiency-boiler',
                'name' => [
                    'en' => 'High Efficiency Boiler',
                    'nl' => 'HR CV-ketel',
                ],
                'order' => 7,
            ],
            [
                'slug' => 'heat-pump',
                'short' => 'heat-pump',
                'name' => [
                    'en' => 'Heat Pump',
                    'nl' => 'Warmtepomp',
                ],
                'order' => 8,
            ],
            [
                'slug' => 'solar-panels',
                'short' => 'solar-panels',
                'name' => [
                    'en' => 'Solar Panels',
                    'nl' => 'Zonnepanelen',
                ],
                'order' => 9,
            ],
            [
                'slug' => 'heater',
                'short' => 'heater',
                'name' => [
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

            $insertStepData = [
                'slug' => $step['slug'],
                'short' => $step['short'],
                'name' => json_encode($step['name']),
                'order' => $step['order'],
            ];

            if (isset($step['parent_short'])) {
                $parent = \App\Models\Step::whereShort($step['parent_short'])->first();
                $insertStepData['parent_id'] = $parent->id;
            }

            DB::table('steps')->updateOrInsert(['short' => $step['short']], $insertStepData);
        }

        $allCooperations = \App\Models\Cooperation::all();

        $steps = \App\Models\Step::whereNotIn('short', ['building-data', 'usage-quick-scan', 'living-requirements', 'residential-status'])->get();

        foreach ($allCooperations as $cooperation) {
            foreach ($steps as $step) {
                // Only attach if not available
                if (! $cooperation->steps()->find($step->id) instanceof \App\Models\Step) {
                    $cooperation->steps()->attach($step);
                }
                $cooperation->steps()->updateExistingPivot($step->id, ['order' => $step->order]);
            }
        }
    }
}
