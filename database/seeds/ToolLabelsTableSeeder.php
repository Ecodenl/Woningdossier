<?php

use Illuminate\Database\Seeder;

class ToolLabelsTableSeeder extends Seeder
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
                    'nl' => 'Warmtepomp',
                ],
                'short' => 'heat-pump',
            ],
        ];

        foreach($datas as $data) {
            $data['name'] = json_encode($data['name']);
            DB::table('tool_labels')->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
