<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KeyFigureInsulationFactorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factors = [
            [
                'insulation_grade' => '1.0',
                'insulation_factor' => '1',
                'energy_consumption_per_m2' => '140',
            ],
            [
                'insulation_grade' => '1.0',
                'insulation_factor' => '1.1',
                'energy_consumption_per_m2' => '130',
            ],
            [
                'insulation_grade' => '1.0',
                'insulation_factor' => '1.2',
                'energy_consumption_per_m2' => '120',
            ],
            [
                'insulation_grade' => '1.0',
                'insulation_factor' => '1.3',
                'energy_consumption_per_m2' => '110',
            ],
            [
                'insulation_grade' => '1.0',
                'insulation_factor' => '1.4',
                'energy_consumption_per_m2' => '100',
            ],
            [
                'insulation_grade' => '1.5',
                'insulation_factor' => '1.5',
                'energy_consumption_per_m2' => '90',
            ],
            [
                'insulation_grade' => '1.5',
                'insulation_factor' => '1.6',
                'energy_consumption_per_m2' => '88',
            ],
            [
                'insulation_grade' => '1.5',
                'insulation_factor' => '1.7',
                'energy_consumption_per_m2' => '86',
            ],
            [
                'insulation_grade' => '1.5',
                'insulation_factor' => '1.8',
                'energy_consumption_per_m2' => '84',
            ],
            [
                'insulation_grade' => '1.5',
                'insulation_factor' => '1.9',
                'energy_consumption_per_m2' => '82',
            ],
            [
                'insulation_grade' => '2.0',
                'insulation_factor' => '2.0',
                'energy_consumption_per_m2' => '80',
            ],
            [
                'insulation_grade' => '2.0',
                'insulation_factor' => '2.1',
                'energy_consumption_per_m2' => '78',
            ],
            [
                'insulation_grade' => '2.0',
                'insulation_factor' => '2.2',
                'energy_consumption_per_m2' => '76',
            ],
            [
                'insulation_grade' => '2.0',
                'insulation_factor' => '2.3',
                'energy_consumption_per_m2' => '74',
            ],
            [
                'insulation_grade' => '2.0',
                'insulation_factor' => '2.4',
                'energy_consumption_per_m2' => '72',
            ],
            [
                'insulation_grade' => '2.5',
                'insulation_factor' => '2.5',
                'energy_consumption_per_m2' => '70',
            ],
            [
                'insulation_grade' => '2.5',
                'insulation_factor' => '2.6',
                'energy_consumption_per_m2' => '66',
            ],
            [
                'insulation_grade' => '2.5',
                'insulation_factor' => '2.7',
                'energy_consumption_per_m2' => '62',
            ],
            [
                'insulation_grade' => '2.5',
                'insulation_factor' => '2.8',
                'energy_consumption_per_m2' => '58',
            ],
            [
                'insulation_grade' => '2.5',
                'insulation_factor' => '2.9',
                'energy_consumption_per_m2' => '54',
            ],
            [
                'insulation_grade' => '3.0',
                'insulation_factor' => '3.0',
                'energy_consumption_per_m2' => '50',
            ],
            [
                'insulation_grade' => '3.0',
                'insulation_factor' => '3.1',
                'energy_consumption_per_m2' => '49',
            ],
            [
                'insulation_grade' => '3.0',
                'insulation_factor' => '3.2',
                'energy_consumption_per_m2' => '48',
            ],
            [
                'insulation_grade' => '3.0',
                'insulation_factor' => '3.3',
                'energy_consumption_per_m2' => '47',
            ],
            [
                'insulation_grade' => '3.0',
                'insulation_factor' => '3.4',
                'energy_consumption_per_m2' => '46',
            ],
            [
                'insulation_grade' => '3.5',
                'insulation_factor' => '3.5',
                'energy_consumption_per_m2' => '45',
            ],
            [
                'insulation_grade' => '3.5',
                'insulation_factor' => '3.6',
                'energy_consumption_per_m2' => '44',
            ],
            [
                'insulation_grade' => '3.5',
                'insulation_factor' => '3.7',
                'energy_consumption_per_m2' => '43',
            ],
            [
                'insulation_grade' => '3.5',
                'insulation_factor' => '3.8',
                'energy_consumption_per_m2' => '42',
            ],
            [
                'insulation_grade' => '3.5',
                'insulation_factor' => '3.9',
                'energy_consumption_per_m2' => '41',
            ],
            [
                'insulation_grade' => '4.0',
                'insulation_factor' => '4.0',
                'energy_consumption_per_m2' => '40',
            ],
        ];

        foreach($factors as $factor){
            \DB::table('key_figure_insulation_factors')->updateOrInsert($factor);
        }
    }
}
