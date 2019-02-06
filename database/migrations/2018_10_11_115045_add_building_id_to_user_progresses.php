<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuildingIdToUserProgresses extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_progresses', function (Blueprint $table) {
			if (!Schema::hasColumn('user_progresses', 'building_id')) {
				$table->integer( 'building_id' )->unsigned()->nullable()->after( 'step_id' );
				$table->foreign( 'building_id' )->references( 'id' )->on( 'buildings' )->onDelete( 'cascade' );
			}
		});

		$progresses = DB::table('user_progresses')->get();
		foreach($progresses as $progress){
			$building = \DB::table('buildings')->where('user_id', '=', $progress->user_id)->first();
			DB::table('user_progresses')->where('user_id', '=', $progress->user_id)->update([ 'building_id' => $building->id ]);
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