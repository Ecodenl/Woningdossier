<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PresentShowerWtwsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $presentShowerWtws = [
            [
                'name' => 'Geen',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Aanwezig',
                'calculate_value' => 2,
            ],
        ];

        foreach ($presentShowerWtws as $presentShowerWtw) {
            DB::table('present_shower_wtws')->insert($presentShowerWtw);
        }
    }
}
