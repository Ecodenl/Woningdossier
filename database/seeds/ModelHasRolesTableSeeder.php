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
        $superAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'cooperation-admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'coach']);

        // The default (admin) user is promoted to super user

        $users = \App\Models\User::where('is_admin', 1)->get();
        foreach ($users as $user) {
            $user->assignRole($superAdmin);
        }
    }
}
