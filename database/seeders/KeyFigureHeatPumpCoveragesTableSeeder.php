<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class KeyFigureHeatPumpCoveragesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $temperatureQuestion = DB::table('tool_questions')
            ->where('short', 'new-boiler-setting-comfort-heat')
            ->first();

        $high = DB::table('tool_question_custom_values')
            ->where('short', 'temp-high')
            ->where('tool_question_id', $temperatureQuestion->id)
            ->first()->id;
        $fifty = DB::table('tool_question_custom_values')
            ->where('short', 'temp-50')
            ->where('tool_question_id', $temperatureQuestion->id)
            ->first()->id;
        $low = DB::table('tool_question_custom_values')
            ->where('short', 'temp-low')
            ->where('tool_question_id', $temperatureQuestion->id)
            ->first()->id;

        $coverages = [
            [
                'betafactor'                    => 1.00,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 85,
            ],
            [
                'betafactor'                    => 1.00,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 95,
            ],
            [
                'betafactor'                    => 1.00,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 100,
            ],
            [
                'betafactor'                    => 0.9,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 85,
            ],
            [
                'betafactor'                    => 0.9,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 93,
            ],
            [
                'betafactor'                    => 0.9,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 98,
            ],
            [
                'betafactor'                    => 0.8,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 85,
            ],
            [
                'betafactor'                    => 0.8,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 92,
            ],
            [
                'betafactor'                    => 0.8,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 97,
            ],
            [
                'betafactor'                    => 0.7,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 80,
            ],
            [
                'betafactor'                    => 0.7,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 90,
            ],
            [
                'betafactor'                    => 0.7,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 95,
            ],
            [
                'betafactor'                    => 0.6,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 70,
            ],
            [
                'betafactor'                    => 0.6,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 89,
            ],
            [
                'betafactor'                    => 0.6,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 94,
            ],
            [
                'betafactor'                    => 0.5,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 60,
            ],
            [
                'betafactor'                    => 0.5,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 87,
            ],
            [
                'betafactor'                    => 0.5,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 92,
            ],
            [
                'betafactor'                    => 0.4,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 50,
            ],
            [
                'betafactor'                    => 0.4,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 86,
            ],
            [
                'betafactor'                    => 0.4,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 91,
            ],
            [
                'betafactor'                    => 0.3,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 40,
            ],
            [
                'betafactor'                    => 0.3,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 76,
            ],
            [
                'betafactor'                    => 0.3,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 80,
            ],
            [
                'betafactor'                    => 0.2,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 30,
            ],
            [
                'betafactor'                    => 0.2,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 48,
            ],
            [
                'betafactor'                    => 0.2,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 50,
            ],
            [
                'betafactor'                    => 0.1,
                'tool_question_custom_value_id' => $high,
                'percentage'                    => 20,
            ],
            [
                'betafactor'                    => 0.1,
                'tool_question_custom_value_id' => $fifty,
                'percentage'                    => 29,
            ],
            [
                'betafactor'                    => 0.1,
                'tool_question_custom_value_id' => $low,
                'percentage'                    => 30,
            ],
        ];

        foreach($coverages as $coverage){
            DB::table('key_figure_heat_pump_coverages')->updateOrInsert(
                Arr::only($coverage, ['betafactor', 'tool_question_custom_value_id']),
                $coverage
            );
        }
    }
}
