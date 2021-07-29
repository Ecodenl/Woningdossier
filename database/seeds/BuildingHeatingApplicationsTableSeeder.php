<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingHeatingApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildingHeatingApplications = [
            [
                'name' => [
                    'nl' => 'Met gewone radiatoren',
                ],
                'short' => 'radiators',
                'calculate_value' => 1,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Met gewone radiatoren en vloerverwarming',
                ],
                'short' => 'radiators-with-floor-heating',
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Met lage temperatuur radiatoren',
                ],
                'short' => 'low-temperature-heater',
                'calculate_value' => 3,
                'order' => 2,
            ],
            [
                'name' => [
                    'nl' => 'Met vloer- en/of wandverwarming',
                ],
                'short' => 'floor-wall-heating',
                'calculate_value' => 4,
                'order' => 3,
            ],
        ];

        foreach ($buildingHeatingApplications as $buildingHeatingApplication) {
            DB::table('building_heating_applications')->insert([
                'name' => json_encode($buildingHeatingApplication['name']),
                'short' => $buildingHeatingApplication['short'],
                'calculate_value' => $buildingHeatingApplication['calculate_value'],
                'order' => $buildingHeatingApplication['order'],
            ]);
        }
    }
}
