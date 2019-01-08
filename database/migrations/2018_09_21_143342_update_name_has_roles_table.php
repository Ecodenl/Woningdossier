<?php

use Illuminate\Database\Migrations\Migration;

class UpdateNameHasRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('roles')
            ->where('name', 'bewoner')
            ->update(['name' => 'resident']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')
            ->where('name', 'resident')
            ->update(['name' => 'bewoner']);
    }
}
