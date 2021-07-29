<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoofTileStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'name' => [
                    'nl' => 'Helemaal nieuw',
                ],
                'calculate_value' => 100,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Bijna nieuw',
                ],
                'calculate_value' => 90,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'In goede staat',
                ],
                'calculate_value' => 80,
                'order' => 2,
            ],
            [
                'name' => [
                    'nl' => 'In redelijk goede staat',
                ],
                'calculate_value' => 70,
                'order' => 3,
            ],
            [
                'name' => [
                    'nl' => 'In redelijke staat',
                ],
                'calculate_value' => 60,
                'order' => 4,
            ],
            [
                'name' => [
                    'nl' => 'Onbekend',
                ],
                'calculate_value' => 50,
                'order' => 5,
            ],
            [
                'name' => [
                    'nl' => 'In matige staat',
                ],
                'calculate_value' => 25,
                'order' => 6,
            ],
            [
                'name' => [
                    'nl' => 'Op termijn aan vervanging toe',
                ],
                'calculate_value' => 15,
                'order' => 7,
            ],
            [
                'name' => [
                    'nl' => 'Binnen de komende tijd aan vervanging toe',
                ],
                'calculate_value' => 6,
                'order' => 8,
            ],
            [
                'name' => [
                    'nl' => 'Aan vervanging toe',
                ],
                'calculate_value' => 0,
                'order' => 9,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('roof_tile_statuses')->insert([
                'name' => json_encode($status['name']),
                'calculate_value' => $status['calculate_value'],
                'order' => $status['order'],
            ]);
        }
    }
}
