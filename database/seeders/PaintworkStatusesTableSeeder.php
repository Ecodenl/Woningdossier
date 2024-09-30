<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaintworkStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => [
                    'nl' => 'Nee',
                ],
                'calculate_value' => 7,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Een beetje',
                ],
                'calculate_value' => 3,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Ja',
                ],
                'calculate_value' => 1,
                'order' => 2,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('paintwork_statuses')->insert([
                'name' => json_encode($status['name']),
                'calculate_value' => $status['calculate_value'],
                'order' => $status['order'],
            ]);
        }
    }
}
