<?php

use Illuminate\Database\Seeder;

class CooperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cooperations = [
            [
                'name' => 'Hoom',
                'slug' => 'hoom',
            ],
            [
                'name' => 'Het Nieuwe Wonen Rivierenland',
                'slug' => 'hnwr',
            ],
            [
                'name' => 'BRED Breda',
                'slug' => 'bresbreda',
            ],
            [
                'name' => 'Duurzaam Garenkokerskwartier',
                'slug' => 'garenkokerskwartier',
            ],
            [
                'name' => 'DE Ramplaan',
                'slug' => 'deramplaan',
            ],
        ];

        foreach ($cooperations as $cooperation) {
            DB::table('cooperations')->insert([
                'name' => $cooperation['name'],
                'slug' => $cooperation['slug'],
            ]);
        }
    }
}
