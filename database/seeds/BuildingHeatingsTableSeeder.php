<?php

use Illuminate\Database\Seeder;
use App\Models\BuildingHeating;

class BuildingHeatingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildingHeatings = [
            [
                'name' => 'Verwarmd, de meeste radiatoren staan aan',
                'degree' => 18,
                'calculate_value' => 2,
            ],
            [
                'name' => 'Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog',
                'degree' => 13,
                'calculate_value' => 3,
            ],
            [
                'name' => 'Onverwarmd, de radiatoren staan op * of uit',
                'degree' => 10,
                'calculate_value' => 4,
            ],
            [
                'name' => 'Niet van toepassing',
                'degree' => null,
                'calculate_value' => null,
            ]
        ];

        foreach ($buildingHeatings as $buildingHeating) {
            BuildingHeating::create([
                'name' => $buildingHeating['name'],
                'degree' => $buildingHeating['degree'],
                'calculate_value' => $buildingHeating['calculate_value']
            ]);
        }
    }
}
