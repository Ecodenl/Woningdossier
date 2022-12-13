<?php

namespace Database\Seeders;

use App\Models\Scan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ScansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $scans = [
            [
                'name' => [
                    'nl' => 'Expert scan',
                ],
                'slug' => [
                    'nl' => 'expert-scan',
                ],
                'short' => Scan::EXPERT,
            ],
            [
                'name' => [
                    'nl' => 'Uitgebreide variant'
                ],
                'slug' => [
                    'nl' => 'quick-scan',
                ],
                'short' => Scan::QUICK,
            ],
            [
                'name' => [
                    'nl' => 'Eenvoudige variant'
                ],
                'slug' => [
                    'nl' => 'lite-scan',
                ],
                'short' => Scan::LITE,
            ],
        ];

        foreach ($scans as $scan) {
            $scan['slug'] = json_encode($scan['slug']);
            $scan['name'] = json_encode($scan['name']);

            DB::table('scans')->updateOrInsert(['short' => $scan['short']], $scan);
        }
    }
}
