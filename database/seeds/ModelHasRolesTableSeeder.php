<?php

use Illuminate\Database\Seeder;

class ModelHasRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $superAdmin = \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'super-admin',
            ],
            [
                'name' => 'super-admin',
                'human_readable_name' => 'Super admin'
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'superuser',
            ],
            [
                'name' => 'superuser',
                'human_readable_name' => 'Super user'
            ]
        );

        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'cooperation-admin',
            ],
            [
                'name' => 'cooperation-admin',
                'human_readable_name' => 'Coöperation admin'
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'coach',
            ],
            [
                'name' => 'coach',
                'human_readable_name' => 'coach'
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'bewoner',
            ],
            [
                'name' => 'resident',
                'human_readable_name' => 'bewoner',
            ]
        );
        \Spatie\Permission\Models\Role::updateOrCreate(
            [
                'name' => 'coordinator',
            ],
            [
                'name' => 'coordinator',
                'human_readable_name' => 'coördinator'
            ]
        );

        // The default (admin) user is promoted to super user

        $users = \App\Models\User::where('is_admin', 1)->get();
        foreach($users as $user){
            $user->assignRole($superAdmin);
        }
    }
}
