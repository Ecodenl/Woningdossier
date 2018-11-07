<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveRoleIdFromBuildingPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('building_permissions', function (Blueprint $table) {
	    	$table->dropForeign('building_permissions_role_id_foreign');
		    $table->dropColumn('role_id');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('building_permissions', function (Blueprint $table) {
		    $table->integer('role_id')->unsigned()->nullable();
		    $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
	    });
    }
}
