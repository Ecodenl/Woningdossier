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
                'name' => json_encode([
                    'nl' => 'Expert modus',
                ]),
                'short' => 'expert-mode',
            ],
            [
                'name' => json_encode([
                    'nl' => 'Quick scan'
                ]),
                'short' => 'quick-scan',
            ]
        ];

        DB::table('scans')->insert($scans);
    }
}
