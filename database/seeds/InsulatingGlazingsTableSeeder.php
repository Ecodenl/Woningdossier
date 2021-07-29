<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsulatingGlazingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insulatings = [
            [
                'name' => [
                    'nl' => 'Enkelglas',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Dubbelglas / voorzetraam',
                ],
            ],
        ];

        foreach ($insulatings as $insulating) {
            DB::table('insulating_glazings')->insert([
                'name' => json_encode($insulating['name']),
            ]);
        }
    }
}
