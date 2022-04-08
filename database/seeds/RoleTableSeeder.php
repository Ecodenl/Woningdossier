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
        $superAdmin = \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'super-admin',
            ],
            [
                'name' => 'super-admin',
                'human_readable_name' => 'Super admin',
                'level' => 100,
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'superuser',
            ],
            [
                'name' => 'superuser',
                'human_readable_name' => 'Super user',
                'level' => 90,
            ]
        );

        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'cooperation-admin',
            ],
            [
                'name' => 'cooperation-admin',
                'human_readable_name' => 'Coöperatie admin',
                'level' => 20,
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'coach',
            ],
            [
                'name' => 'coach',
                'human_readable_name' => 'Coach',
                'level' => 5,
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'resident',
            ],
            [
                'name' => 'resident',
                'human_readable_name' => 'Bewoner',
                'level' => 1,
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'coordinator',
            ],
            [
                'name' => 'coordinator',
                'human_readable_name' => 'Coördinator',
                'level' => 15,
            ]
        );


        $coachInputSource = \App\Models\InputSource::where('short', 'coach')->first();
        DB::table('roles')->where('name', 'coach')->update(['input_source_id' => $coachInputSource->id]);

        $residentInputSource = \App\Models\InputSource::where('short', 'resident')->first();
        DB::table('roles')->where('name', 'resident')->update(['input_source_id' => $residentInputSource->id]);

        $cooperationInputSource = \App\Models\InputSource::where('short', 'cooperation')->first();
        DB::table('roles')->where('name', 'coordinator')->update(['input_source_id' => $cooperationInputSource->id]);
        DB::table('roles')->where('name', 'cooperation-admin')->update(['input_source_id' => $cooperationInputSource->id]);
    }
}
