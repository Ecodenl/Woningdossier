<?php

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
                'short' => 'expert-scan',
            ],
            [
                'name' => [
                    'nl' => 'Quick scan'
                ],
                'short' => 'quick-scan',
            ]
        ];

        foreach ($scans as $scan) {
            DB::table('scans')->updateOrInsert(['short' => $scan['short']], $scan);
        }
    }
}
