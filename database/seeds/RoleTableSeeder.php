x<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coachInputSource = \App\Models\InputSource::where('short', 'coach')->first();
        DB::table('roles')->where('name', 'coach')->update(['input_source_id' => $coachInputSource->id]);

        $residentInputSource = \App\Models\InputSource::where('short', 'resident')->first();
        DB::table('roles')->where('name', 'resident')->update(['input_source_id' => $residentInputSource->id]);

        $cooperationInputSource = \App\Models\InputSource::where('short', 'cooperation')->first();
        DB::table('roles')->where('name', 'coordinator')->update(['input_source_id' => $cooperationInputSource->id]);
        DB::table('roles')->where('name', 'cooperation-admin')->update(['input_source_id' => $cooperationInputSource->id]);
    }
}
