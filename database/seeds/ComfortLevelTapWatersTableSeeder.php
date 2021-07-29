<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComfortLevelTapWatersTableSeeder extends Seeder
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
                    'nl' => 'Standaard',
                ],
                'calculate_value' => 1,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Comfort',
                ],
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Comfort plus',
                ],
                'calculate_value' => 3,
                'order' => 2,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('comfort_level_tap_waters')->insert([
                'name' => json_encode($status['name']),
                'calculate_value' => $status['calculate_value'],
                'order' => $status['order'],
            ]);
        }
    }
}
