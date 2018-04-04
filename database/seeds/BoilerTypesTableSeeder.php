<?php

use Illuminate\Database\Seeder;

class BoilerTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'name' => 'Conventioneel rendement ketel',
                'calculate_value' => null,
            ],
            [
                'name' => 'Verbeterd rendement ketel',
                'calculate_value' => null,
            ],
            [
                'name' => 'HR100 Ketel',
                'calculate_value' => null,
            ],
            [
                'name' => 'HR104 Ketel',
                'calculate_value' => null,
            ],
            [
                'name' => 'HR107 Ketel',
                'calculate_value' => null,
            ],
        ];

        foreach ($types as $type) {
            \App\Models\BoilerType::create($type);
        }
    }
}
