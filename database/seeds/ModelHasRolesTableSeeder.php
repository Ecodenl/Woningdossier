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

        // The default (admin) user is promoted to super user

        $accounts = DB::table('accounts')->where('is_admin', '=', 1)->get();


        foreach($accounts as $account) {

            $users = DB::table('users')->where('account_id', '=', $account->id)->get();

            foreach ($users as $user) {

                $isSuperAdmin = DB::table('model_has_roles')
                  ->where('model_type', 'App\Models\User')
                    ->where('model_id', '=', $user->id)
                    ->where('role_id', '=', 1)
                    ->exists();

                if (!$isSuperAdmin){
                    DB::table('model_has_roles')->insert([
                        'model_type' => 'App\Models\User',
                        'model_id' => $user->id,
                        'role_id' => 1,
                    ]);
                }

            }
        }
    }
}
