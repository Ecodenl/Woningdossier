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
        $labels = [
            [
                'name' => [
                    'nl' => 'HR CV Ketel',
                ],
                'short' => 'hr-boiler',
            ],
            [
                'name' => [
                    'nl' => 'Warmtepomp',
                ],
                'short' => 'heat-pump',
            ],
            [
                'name' => [
                    'nl' => 'Zonneboiler',
                ],
                'short' => 'sun-boiler',
            ],
        ];

        foreach($labels as $data) {
            $data['name'] = json_encode($data['name']);
            DB::table('tool_labels')->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
