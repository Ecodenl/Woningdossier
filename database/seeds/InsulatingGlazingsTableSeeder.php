<?php

use Illuminate\Database\Seeder;

class InsulatingGlazingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insulating = ['Enkelglas', 'Dubbelglas / voorzetraam'];

        foreach ($insulating as $insulate) {
            \App\Models\InsulatingGlazing::create(['name' => $insulate]);
        }
    }
}
