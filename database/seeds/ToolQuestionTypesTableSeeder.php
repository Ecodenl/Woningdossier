<?php

use Illuminate\Database\Seeder;

class ToolQuestionTypesTableSeeder extends Seeder
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
                'name'  => ['nl' => 'Keuze met icon'],
                'short' => 'radio-icon'
            ],
            [
                'name'  => ['nl' => 'Keuze met icon, klein en zonder tekst'],
                'short' => 'radio-icon-small'
            ],
            [
                'name'  => ['nl' => 'Keuze veld'],
                'short' => 'radio'
            ],
            [
                'name'  => ['nl' => 'Slider'],
                'short' => 'slider'
            ],
            [
                'name'  => ['nl' => 'Invulveld'],
                'short' => 'text'
            ],
            [
                'name'  => ['nl' => 'Tekstvak'],
                'short' => 'textarea'
            ],
            [
                'name' => ['nl' => 'Prioriteiten meter'],
                'short' => 'measure-priority'
            ],
        ];

        foreach ($datas as $data) {
            $data['name'] = json_encode($data['name']);
            DB::table('tool_question_types')
                ->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
