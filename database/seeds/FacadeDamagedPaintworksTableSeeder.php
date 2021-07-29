<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacadeDamagedPaintworksTableSeeder extends Seeder
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
                    'nl' => 'Nee',
                ],
                'calculate_value' => 0,
                'order' => 0,
                'term_years' => 15,
            ],
            [
                'name' => [
                    'nl' => 'Een beetje',
                ],
                'calculate_value' => 3,
                'order' => 1,
                'term_years' => 7,
            ],
            [
                'name' => [
                    'nl' => 'Ja',
                ],
                'calculate_value' => 5,
                'order' => 2,
                'term_years' => 0,
            ],
        ];

        foreach ($items as $item) {
            DB::table('facade_damaged_paintworks')->insert([
                'name' => json_encode($item['name']),
                'calculate_value' => $item['calculate_value'],
                'order' => $item['order'],
                'term_years' => $item['term_years'],
            ]);
        }
    }
}
