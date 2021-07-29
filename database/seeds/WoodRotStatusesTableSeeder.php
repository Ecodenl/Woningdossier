<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WoodRotStatusesTableSeeder extends Seeder
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
                    'nl' => 'Nee',
                ],
                'calculate_value' => null,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Een beetje',
                ],
                'calculate_value' => 3, // year
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Ja',
                ],
                'calculate_value' => 1, // year
                'order' => 2,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('wood_rot_statuses')->insert([
                'name' => json_encode($status['name']),
                'calculate_value' => $status['calculate_value'],
                'order' => $status['order'],
            ]);
        }
    }
}
