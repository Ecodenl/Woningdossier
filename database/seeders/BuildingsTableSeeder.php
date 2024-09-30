<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BuildingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('buildings')->insert([
            'user_id' => 1,
            'street' => 'Hoekzijdsestraatweg',
            'number' => 1,
            'city' => 'Lutjebroek',
            'postal_code' => '1821 AB',
            'country_code' => 'nl',
            'bag_addressid' => '0580010000253148',
        ]);
    }
}
