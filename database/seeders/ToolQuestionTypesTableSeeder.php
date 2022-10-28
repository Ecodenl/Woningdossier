<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
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
                'name' => ['nl' => 'Meerkeuze met icon'],
                'short' => 'checkbox-icon',
            ],
            [
                'name' => ['nl' => 'Keuze met icon'],
                'short' => 'radio-icon',
            ],
            [
                'name' => ['nl' => 'Keuze met icon, klein en zonder tekst'],
                'short' => 'radio-icon-small',
            ],
            [
                'name' => ['nl' => 'Keuze veld'],
                'short' => 'radio',
            ],
            [
                'name' => ['nl' => 'Slider'],
                'short' => 'slider',
            ],
            [
                'name' => ['nl' => 'Invulveld'],
                'short' => 'text',
            ],
            [
                'name' => ['nl' => 'Tekstvak'],
                'short' => 'textarea',
            ],
            [
                'name' => ['nl' => 'Tekstvak, opent in popup'],
                'short' => 'textarea-popup',
            ],
            [
                'name' => ['nl' => 'Prioriteiten meter'],
                'short' => 'rating-slider',
            ],
            [
                'name' => ['nl' => 'Dropdown'],
                'short' => 'dropdown',
            ],
            [
                'name' => ['nl' => 'Multi dropdown'],
                'short' => 'multi-dropdown',
            ],
        ];

        foreach ($datas as $data) {
            $data['name'] = json_encode($data['name']);
            DB::table('tool_question_types')
                ->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
