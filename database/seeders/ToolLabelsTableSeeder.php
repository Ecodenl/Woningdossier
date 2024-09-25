<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ToolLabelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labels = [
            [
                'name' => [
                    'nl' => 'Standaard energieprijzen: gas = €1,45, elektra = €0,40',
                ],
                'short' => 'gas-electricity-price-explain'
            ],
            [
                'name' => [
                    'nl' => 'Verwarming en warm water',
                ],
                'short' => 'heat-source',
            ],
            [
                'name' => [
                    'nl' => 'HR CV Ketel',
                ],
                'short' => 'hr-boiler',
            ],
            [
                'name' => [
                    'nl' => 'Warmtepomp',
                ],
                'short' => 'heat-pump',
            ],
            [
                'name' => [
                    'nl' => 'Zonneboiler',
                ],
                'short' => 'sun-boiler',
            ],
            [
                'name' => [
                    'nl' => 'Indicatie voor kosten en baten van de CV-ketel',
                ],
                'short' => 'hr-boiler-cost-indication',
            ],
            [
                'name' => [
                    'nl' => 'Indicatie voor de efficiëntie van de warmtepomp',
                ],
                'short' => 'heat-pump-efficiency-indication',
            ],
            [
                'name' => [
                    'nl' => 'Indicatie voor kosten en baten van de warmtepomp',
                ],
                'short' => 'heat-pump-cost-indication',
            ],
            [
                'name' => [
                    'nl' => 'Geschat huidig gebruik',
                ],
                'short' => 'sun-boiler-estimate-current-usage',
            ],
            [
                'name' => [
                    'nl' => 'Specificaties systeem',
                ],
                'short' => 'sun-boiler-specifications',
            ],
            [
                'name' => [
                    'nl' => 'Indicatie voor kosten en baten van de zonneboiler',
                ],
                'short' => 'sun-boiler-cost-indication',
            ],
            [
                'name' => [
                    'nl' => 'Warmtepomp boiler',
                ],
                'short' => 'heat-pump-boiler',
            ],
            [
                'name' => [
                    'nl' => 'Verlichting',
                ],
                'short' => 'light',
            ],
            [
                'name' => [
                    'nl' => 'Energiezuinige apparatuur',
                ],
                'short' => 'energy-efficient-equipment',
            ],
            [
                'name' => [
                    'nl' => 'Energiezuinige installaties',
                ],
                'short' => 'energy-efficient-installations',
            ],
            [
                'name' => [
                    'nl' => 'Kierdichting',
                ],
                'short' => 'crack-sealing',
            ],
            [
                'name' => [
                    'nl' => 'Verbeteren van de radiatoren',
                ],
                'short' => 'improve-radiators',
            ],
            [
                'name' => [
                    'nl' => 'Verbeteren van de verwarmingsinstallatie',
                ],
                'short' => 'improve-heating-installations',
            ],
            [
                'name' => [
                    'nl' => 'Besparen op warm tapwater',
                ],
                'short' => 'save-warm-tap-water',
            ],
            [
                'name' => [
                    'nl' => 'Algemeen',
                ],
                'short' => 'general',
            ],
        ];

        foreach ($labels as $data) {
            $data['name'] = json_encode($data['name']);
            DB::table('tool_labels')->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
