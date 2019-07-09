<?php

use Illuminate\Database\Migrations\Migration;

class AddAndAssignRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $superAdmin = \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'cooperation-admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'coach']);

        // The default (admin) user is promoted to super user

        $users = \App\Models\User::where('is_admin', 1)->get();
        foreach ($users as $user) {
            $user->assignRole($superAdmin);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
