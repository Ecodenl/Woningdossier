<?php

use Illuminate\Database\Seeder;

class CooperationMeasureApplicationsTableSeeder extends Seeder
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
                'name' => ['nl' => 'Waterbesparende gasleiding'],
                'costs' => ['from' => 50, 'to' => 100],
                'savings_money' => 100,
                'cooperation_id' => \App\Models\Cooperation::first()->id
            ]
        ];

        foreach ($datas as $data) {
            \App\Models\CooperationMeasureApplication::create($data);
        }
    }
}
