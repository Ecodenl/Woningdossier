<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipalitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Voorne aan zee',
                'short' => 'voorne-aan-zee'
            ]
        ];
        DB::table('municipalities')->insert($data);
    }
}
