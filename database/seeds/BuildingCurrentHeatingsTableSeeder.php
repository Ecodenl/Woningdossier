<?php

use Illuminate\Database\Seeder;

class BuildingCurrentHeatingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $heatings = [
            [
                'name' => 'Met normale radiatoren',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Met normale radiatoren en vloerverwarming',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Alleen met vloerverwarming',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Met lage temperatuur radiatoren en vloerverwarming',
                'calculate_value' => 4,
            ],
            [
                'name' => 'Met lage temperatuur radiatoren',
                'calculate_value' => 5,
            ],
        ];

        foreach ($heatings as $heating) {
            \App\Models\BuildingCurrentHeating::create($heating);
        }
    }
}
