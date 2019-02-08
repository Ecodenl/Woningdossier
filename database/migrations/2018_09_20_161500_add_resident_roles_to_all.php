<?php

use Illuminate\Database\Migrations\Migration;

class AddResidentRolesToAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $records = DB::table('model_has_roles')
          ->rightJoin('users', 'model_has_roles.model_id', 'users.id')
            ->whereNull('model_has_roles.role_id')
            ->get(['users.id', 'users.is_admin']);

        // Get the lowest level
        $role = DB::table('roles')->orderBy('level', 'ASC')->first();

        /** @var \stdClass $record */
        foreach ($records as $record) {
            DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_id' => $record->id,
                'model_type' => get_class(new \App\Models\User()),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // do nothing. Keep the records
    }
}
