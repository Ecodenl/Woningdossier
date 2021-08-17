<?php

use Illuminate\Database\Seeder;

class EnergyLabelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $energyLabels = [
            [
                'name' => 'A',
                'country_code' => 'nl',
            ],
            [
                'name' => 'B',
                'country_code' => 'nl',
            ],
            [
                'name' => 'C',
                'country_code' => 'nl',
            ],
            [
                'name' => 'D',
                'country_code' => 'nl',
            ],
            [
                'name' => 'E',
                'country_code' => 'nl',
            ],
            [
                'name' => 'F',
                'country_code' => 'nl',
            ],
            [
                'name' => 'G',
                'country_code' => 'nl',
            ],
            [
                'name' => '?',
                'country_code' => 'nl'
            ],
        ];

        foreach ($energyLabels as $order => $energyLabel) {
            \DB::table('energy_labels')->updateOrInsert(
                [
                    'name' => $energyLabel['name']
                ],
                [
                    'order' => $order,
                    'name' => $energyLabel['name'],
                    'country_code' => $energyLabel['country_code'],
                ]
            );
        }
    }
}
