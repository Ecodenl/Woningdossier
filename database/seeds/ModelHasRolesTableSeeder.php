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

        // The default (admin) user is promoted to super user

        $accounts = DB::table('accounts')->where('is_admin', '=', 1)->get();

        foreach ($accounts as $account) {
            $users = DB::table('users')->where('account_id', '=', $account->id)->get();

            foreach ($users as $user) {
                $isSuperAdmin = DB::table('model_has_roles')
                  ->where('model_type', \App\Models\User::class)
                    ->where('model_id', '=', $user->id)
                    ->where('role_id', '=', 1)
                    ->exists();

                if (! $isSuperAdmin) {
                    DB::table('model_has_roles')->insert([
                        'model_type' => \App\Models\User::class,
                        'model_id' => $user->id,
                        'role_id' => 1,
                    ]);
                }
            }
        }
    }
}
