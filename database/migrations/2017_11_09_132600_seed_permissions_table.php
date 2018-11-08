<?php

use Illuminate\Database\Migrations\Migration;

class SeedPermissionsTable extends Migration
{
    protected $permissions = [
        'create_person',
        'view_person',
        'update_person',
        'delete_person',
        'create_organisation',
        'view_organisation',
        'update_organisation',
        'delete_organisation',
        'update_contact_iban',
        'update_contact_owner',
        'manage_group',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->permissions as $permissionName) {
            \Spatie\Permission\Models\Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]
            );
        }

        $superuserRole = \Spatie\Permission\Models\Role::create([
            'name' => 'superuser',
            'guard_name' => 'web',
        ]);

        $superuserRole->syncPermissions(\Spatie\Permission\Models\Permission::all());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $superUserRole = \Spatie\Permission\Models\Role::findByName('superuser', 'web');
        if ($superUserRole instanceof \Spatie\Permission\Models\Role) {
            $superUserRole->revokePermissionTo(\Spatie\Permission\Models\Permission::all());
        }
        try {
            $superUserRole->delete();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        foreach ($this->permissions as $permissionName) {
            \Spatie\Permission\Models\Permission::findByName($permissionName)->delete();
        }
    }
}
