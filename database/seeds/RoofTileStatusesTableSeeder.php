<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Helemaal nieuw',
                ],
                'calculate_value' => 100,
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Bijna nieuw',
                ],
                'calculate_value' => 90,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'In goede staat',
                ],
                'calculate_value' => 80,
                'order' => 2,
            ],
            [
                'names' => [
                    'nl' => 'In redelijk goede staat',
                ],
                'calculate_value' => 70,
                'order' => 3,
            ],
            [
                'names' => [
                    'nl' => 'In redelijke staat',
                ],
                'calculate_value' => 60,
                'order' => 4,
            ],
            [
                'names' => [
                    'nl' => 'Onbekend',
                ],
                'calculate_value' => 50,
                'order' => 5,
            ],
            [
                'names' => [
                    'nl' => 'In matige staat',
                ],
                'calculate_value' => 25,
                'order' => 6,
            ],
            [
                'names' => [
                    'nl' => 'Op termijn aan vervanging toe',
                ],
                'calculate_value' => 15,
                'order' => 7,
            ],
            [
                'names' => [
                    'nl' => 'Binnen de komende tijd aan vervanging toe',
                ],
                'calculate_value' => 6,
                'order' => 8,
            ],
            [
                'names' => [
                    'nl' => 'Aan vervanging toe',
                ],
                'calculate_value' => 0,
                'order' => 9,
            ],
        ];

        foreach ($statuses as $status) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($status['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('roof_tile_statuses')->insert([
                'name' => $uuid,
                'calculate_value' => $status['calculate_value'],
                'order' => $status['order'],
            ]);
        }
    }
}
