<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrawlspaceAccessesTableSeeder extends Seeder
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
                    'nl' => 'Ja',
                ],
                'calculate_value' => 0,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Nee',
                ],
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Gedeeltelijk',
                ],
                'calculate_value' => 1,
                'order' => 2,
            ],
        ];

        foreach ($items as $item) {
            DB::table('crawlspace_accesses')->insert([
                'name' => json_encode($item['name']),
                'calculate_value' => $item['calculate_value'],
                'order' => $item['order'],
            ]);
        }
    }
}
