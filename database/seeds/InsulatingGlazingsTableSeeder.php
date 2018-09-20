<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Enkelglas',
                ],
            ],
            [
                'names' => [
                    'nl' => 'Dubbelglas / voorzetraam',
                ],
            ],
        ];

        foreach ($insulatings as $insulating) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($insulating['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('insulating_glazings')->insert([
                'name' => $uuid,
            ]);
        }
    }
}
