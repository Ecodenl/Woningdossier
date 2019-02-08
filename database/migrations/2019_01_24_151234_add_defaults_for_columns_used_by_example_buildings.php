<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultsForColumnsUsedByExampleBuildings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	$orientation = \DB::table('pv_panel_orientations')->orderBy('order')->first();

	    Schema::table('building_pv_panels', function (Blueprint $table) use ($orientation) {
		    $table->integer('number')->default(0)->change();
			$table->dropForeign('building_pv_panels_pv_panel_orientation_id_foreign');
		    $table->integer('pv_panel_orientation_id')->unsigned()->nullable()->change();
		    $table->foreign('pv_panel_orientation_id')->references('id')->on('pv_panel_orientations')->onDelete('set null');
	    });
	    Schema::table('building_heaters', function (Blueprint $table) use ($orientation) {
		    $table->dropForeign('building_heaters_pv_panel_orientation_id_foreign');
		    $table->integer('pv_panel_orientation_id')->unsigned()->nullable()->change();
		    $table->foreign('pv_panel_orientation_id')->references('id')->on('pv_panel_orientations')->onDelete('set null');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
