<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacadeSurfacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $surfaces = [
            [
                'name' => [
                    'nl' => 'Nee',
                ],
                'calculate_value' => 0,
                'order' => 0,
                'execution_term_name' => [
                    'nl' => 'Niet nodig',
                ],
                'term_years' => null,
            ],
            [
                'name' => [
                    'nl' => 'Ja, tot 10 m2',
                ],
                'calculate_value' => 10,
                'order' => 1,
                'execution_term_name' => [
                    'nl' => 'Binnen 5 jaar',
                ],
                'term_years' => 5,
            ],
            [
                'name' => [
                    'nl' => 'Ja, 10 m2 tot 25 m2',
                ],
                'calculate_value' => 25,
                'order' => 2,
                'execution_term_name' => [
                    'nl' => 'Binnen 5 jaar',
                ],
                'term_years' => 5,
            ],
            [
                'name' => [
                    'nl' => 'Ja, 25 m2 tot 50 m2',
                ],
                'calculate_value' => 50,
                'order' => 3,
                'execution_term_name' => [
                    'nl' => 'Binnen 1 jaar',
                ],
                'term_years' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Ja, 50 m2 tot 80 m2',
                ],
                'calculate_value' => 80,
                'order' => 4,
                'execution_term_name' => [
                    'nl' => 'Binnen 1 jaar',
                ],
                'term_years' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Ja, meer dan 80 m2',
                ],
                'calculate_value' => 120,
                'order' => 5,
                'execution_term_name' => [
                    'nl' => 'Binnen 1 jaar',
                ],
                'term_years' => 0,
            ],
        ];

        foreach ($surfaces as $surface) {
            DB::table('facade_surfaces')->insert([
                'name' => json_encode($surface['name']),
                'calculate_value' => $surface['calculate_value'],
                'order' => $surface['order'],
                'execution_term_name' => json_encode($surface['execution_term_name']),
                'term_years' => $surface['term_years'],
            ]);
        }
    }
}
