<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputSourceIdToUserActionPlanAdvices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            $table->integer('input_source_id')->unsigned()->nullable()->default(1)->after('user_id');
            $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
            $table->dropForeign('user_action_plan_advices_input_source_id_foreign');
            $table->dropColumn('input_source_id');
        });
    }
}
