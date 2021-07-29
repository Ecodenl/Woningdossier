<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacadePlasteredSurfacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name' => [
                    'nl' => 'Ja, tot 10 m2',
                ],
                'calculate_value' => 10,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Ja, 10 m2 tot 25 m2',
                ],
                'calculate_value' => 25,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Ja, 25 m2 tot 50 m2',
                ],
                'calculate_value' => 50,
                'order' => 2,
            ],
            [
                'name' => [
                    'nl' => 'Ja, 50 m2 tot 80 m2',
                ],
                'calculate_value' => 80,
                'order' => 3,
            ],
            [
                'name' => [
                    'nl' => 'Ja, meer dan 80 m2',
                ],
                'calculate_value' => 120,
                'order' => 4,
            ],
        ];

        foreach ($items as $item) {
            DB::table('facade_plastered_surfaces')->insert([
                'name' => json_encode($item['name']),
                'calculate_value' => $item['calculate_value'],
                'order' => $item['order'],
            ]);
        }
    }
}
