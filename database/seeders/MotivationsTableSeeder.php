<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => [
                    'nl' => 'Comfortverbetering',
                ],
                'calculate_value' => null,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Iets voor het milieu doen',
                ],
                'calculate_value' => null,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Verlaging van de maandlasten',
                ],
                'calculate_value' => null,
                'order' => 2,
            ],
            [
                'name' => [
                    'nl' => 'Goed rendement op de investering',
                ],
                'calculate_value' => null,
                'order' => 3,
            ],
        ];

        foreach ($motivations as $motivation) {
           DB::table('motivations')->insert([
                'name' => json_encode($motivation['name']),
                'calculate_value' => $motivation['calculate_value'],
                'order' => $motivation['order'],
            ]);
        }
    }
}
