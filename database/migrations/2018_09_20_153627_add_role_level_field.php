<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleLevelField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('roles', function (Blueprint $table) {
			$table->integer('level')->default(1); // bewoner
	    });
	    Artisan::call('db:seed', array('--class' => 'ModelHasRolesTableSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('roles', function (Blueprint $table) {
		    $table->dropColumn('level');
	    });
    }
}
