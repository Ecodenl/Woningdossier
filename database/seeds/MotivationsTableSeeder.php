<?php

use Illuminate\Database\Seeder;

class MotivationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $motivations = [
            [
                'names' => [
                    'nl' => 'Comfortverbetering',
                ],
                'calculate_value' => null,
                'order' => 0,
            ],
            [
                'names' => [
                    'nl' => 'Iets voor het milieu doen',
                ],
                'calculate_value' => null,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Verlaging van de maandlasten',
                ],
                'calculate_value' => null,
                'order' => 2,
            ],
            [
                'names' => [
                    'nl' => 'Goed rendement op de investering',
                ],
                'calculate_value' => null,
                'order' => 3,
            ],
        ];

        foreach ($motivations as $motivation) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($motivation['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('motivations')->insert([
                'name' => $uuid,
                'calculate_value' => $motivation['calculate_value'],
                'order' => $motivation['order'],
            ]);
        }
    }
}
