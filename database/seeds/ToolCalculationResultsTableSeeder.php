<?php

use Illuminate\Database\Seeder;

class ToolCalculationResultsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'name' => [
                    'nl' => 'Besparing gas HR ketel'
                ],
                'short' => 'hr-boiler.savings_gas',
                'unit_of_measure' => 'GASS',
            ],
            [
                'name' => [
                    'nl' => 'Besparing gas HEAT PUMPe '
                ],
                'short' => 'heat-pump.savings_gas',
                'unit_of_measure' => 'GAz',
            ],
        ];

        foreach($datas as $data) {
            DB::table('tool_calculation_results')->updateOrInsert(
                [
                    'short' => $data['short']
                ],
                [
                    'name' => json_encode($data['name']),
                    'unit_of_measure' => $data['unit_of_measure'],
                ],
            );
        }
    }
}
