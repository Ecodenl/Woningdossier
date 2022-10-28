<?php

namespace Database\Seeders;

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
                'short' => 'expert-scan',
            ],
            [
                'name' => [
                    'nl' => 'Quick scan'
                ],
                'slug' => [
                    'nl' => 'quick-scan',
                ],
                'short' => 'quick-scan',
            ]
        ];

        foreach ($scans as $scan) {
            $scan['slug'] = json_encode($scan['slug']);
            $scan['name'] = json_encode($scan['name']);

            DB::table('scans')->updateOrInsert(['short' => $scan['short']], $scan);
        }
    }
}
