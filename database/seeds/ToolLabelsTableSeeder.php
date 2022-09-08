<?php

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
        ];

        foreach($labels as $data) {
            $data['name'] = json_encode($data['name']);
            DB::table('tool_labels')->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
