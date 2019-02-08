<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInputSourceIdToToolSettings extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tool_settings', function (Blueprint $table) {
			if (! Schema::hasColumn('tool_settings', 'input_source_id')) {
				$table->integer('input_source_id')->unsigned()->nullable()->default(1)->after('changed_input_source_id');
				$table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');
			}
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tool_settings', function (Blueprint $table) {
			$table->dropForeign('user_interests_input_source_id_foreign');
			$table->dropColumn('input_source_id');
		});
	}
}
