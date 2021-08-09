<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMeasureApplicationIdOnUserActionPlanAdvicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_action_plan_advices', function (Blueprint $table) {



            $name = 'measureable';

            $table->string("{$name}_type");
            $table->unsignedBigInteger("{$name}_id");
            $table->index(["{$name}_type", "{$name}_id"]);

//            $table->dropForeign(['measure_application_id']);
//            $table->dropColumn('measure_application_id');
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
            $table->dropMorphs('measureable');
        });
    }
}
