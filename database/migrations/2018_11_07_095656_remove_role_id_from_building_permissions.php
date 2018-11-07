<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveRoleIdFromBuildingPermissions extends Migration
{

	/**
	 * Only applies to accept and local currently. So no rollback is required.
	 */

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

		    $table->dropForeign('building_permissions_step_id_foreign');
		    $table->dropColumn('step_id');

		    $table->dropColumn('permissions');
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

		    $table->integer('step_id')->unsigned()->nullable();
		    $table->foreign('step_id')->references('id')->on('steps')->onDelete('set null');

		    $table->text('permissions');
	    });
    }
}
