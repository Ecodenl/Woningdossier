<?php

use Illuminate\Database\Seeder;

class DamageToPaintWorksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $damges = [
            [
                'name' => 'Nee',
                'calculate_value' => 0
            ],
            [
                'name' => 'Ja, een beetje',
                'calculate_value' => 3
            ],
            [
                'name' => 'Ja, heel erg',
                'calculate_value' => 5
            ],
        ];

        foreach ($damges as $damage) {
            \App\Models\DamageToPaintWork::create($damage);
        }
    }
}
