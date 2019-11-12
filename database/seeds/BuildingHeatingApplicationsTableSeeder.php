<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Met gewone radiatoren',
                ],
                'short' => 'radiators',
                'calculate_value' => 1,
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Met gewone radiatoren en vloerverwarming',
                ],
                'short' => 'radiators-with-floor-heating',
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Met lage temperatuur radiatoren',
                ],
                'short' => 'low-temperature-heater',
                'calculate_value' => 3,
                'order' => 2,
            ],
            [
                'names' => [
                    'nl' => 'Met vloer- en/of wandverwarming',
                ],
                'short' => 'floor-wall-heating',
                'calculate_value' => 4,
                'order' => 3,
            ],
        ];

        foreach ($buildingHeatingApplications as $buildingHeatingApplication) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($buildingHeatingApplication['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('building_heating_applications')->insert([
                'name' => $uuid,
                'short' => $buildingHeatingApplication['short'],
                'calculate_value' => $buildingHeatingApplication['calculate_value'],
                'order' => $buildingHeatingApplication['order'],
            ]);
        }
    }
}
