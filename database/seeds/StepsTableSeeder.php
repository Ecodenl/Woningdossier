<?php

use App\Scopes\NoGeneralDataScope;
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

        $expertScan = DB::table('scans')->where('short', 'expert-scan')->first();
        $quickScan = DB::table('scans')->where('short', 'quick-scan')->first();

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
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Ventilation',
                    'nl' => 'Ventilatie',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'wall-insulation',
                'short' => 'wall-insulation',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Wall Insulation',
                    'nl' => 'Gevelisolatie',
                ],
                'order' => 3,
            ],
            [
                'slug' => 'insulated-glazing',
                'short' => 'insulated-glazing',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Insulated Glazing',
                    'nl' => 'Isolerende beglazing',
                ],
                'order' => 4,
            ],
            [
                'slug' => 'floor-insulation',
                'short' => 'floor-insulation',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Floor Insulation',
                    'nl' => 'Vloerisolatie',
                ],
                'order' => 5,
            ],
            [
                'slug' => 'roof-insulation',
                'short' => 'roof-insulation',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Roof Insulation',
                    'nl' => 'Dakisolatie',
                ],
                'order' => 6,
            ],
            [
                'slug' => 'high-efficiency-boiler',
                'short' => 'high-efficiency-boiler',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'High Efficiency Boiler',
                    'nl' => 'HR CV-ketel',
                ],
                'order' => 7,
            ],
            [
                'slug' => 'heat-pump',
                'short' => 'heat-pump',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Heat Pump',
                    'nl' => 'Warmtepomp',
                ],
                'order' => 8,
            ],
            [
                'slug' => 'solar-panels',
                'short' => 'solar-panels',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Solar Panels',
                    'nl' => 'Zonnepanelen',
                ],
                'order' => 9,
            ],
            [
                'slug' => 'heater',
                'short' => 'heater',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Heater',
                    'nl' => 'Zonneboiler',
                ],
                'order' => 10,
            ],
            [
                'slug' => 'verwarming',
                'short' => 'heating',
                'scan_id' => $expertScan->id,
                'name' => [
                    'en' => 'Heating',
                    'nl' => 'Verwarming',
                ],
                'order' => 11,
            ],
            // the steps used for the "quick scan"
            [
                'slug' => 'woninggegevens',
                'short' => 'building-data',
                'scan_id' => $quickScan->id,
                'name' => [
                    'en' => 'Building data',
                    'nl' => 'Woninggegevens',
                ],
                'order' => 0,
            ],
            [
                'slug' => 'bewoners-gebruik',
                'short' => 'usage-quick-scan',
                'scan_id' => $quickScan->id,
                'name' => [
                    'en' => 'Usage',
                    'nl' => 'Gebruik',
                ],
                'order' => 1,
            ],
            [
                'slug' => 'woonwensen',
                'short' => 'living-requirements',
                'scan_id' => $quickScan->id,
                'name' => [
                    'en' => 'Living requirements',
                    'nl' => 'Woonwensen',
                ],
                'order' => 2,
            ],
            [
                'slug' => 'woonstatus',
                'short' => 'residential-status',
                'scan_id' => $quickScan->id,
                'name' => [
                    'en' => 'Residential status',
                    'nl' => 'Woningstatus',
                ],
                'order' => 3,
            ],
        ];

        foreach ($steps as $step) {

            $insertStepData = [
                'slug' => $step['slug'],
                'short' => $step['short'],
                'scan_id' => $step['scan_id'] ?? null,
                'name' => json_encode($step['name']),
                'order' => $step['order'],
            ];

            if (isset($step['parent_short'])) {
                $parent = \App\Models\Step::withoutGlobalScope(NoGeneralDataScope::class)->whereShort($step['parent_short'])->first();
                $insertStepData['parent_id'] = $parent->id;
            }

            DB::table('steps')->updateOrInsert(['short' => $step['short']], $insertStepData);
        }
    }
}
