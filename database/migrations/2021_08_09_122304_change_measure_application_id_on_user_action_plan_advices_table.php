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
            $name = 'user_action_plan_advisable';

            $table->string("{$name}_type")->after('measure_application_id');
            $table->unsignedBigInteger("{$name}_id")->after('user_action_plan_advisable_type');
            $table->index("{$name}_id", "{$name}_id_index");
            $table->index("{$name}_type", "{$name}_type_index");
        });

        // now migrate the measure_application_id to the morph relationship
        foreach (DB::table('user_action_plan_advices')->get() as $userActionPlanAdvice) {
            DB::table('user_action_plan_advices')
                ->where('id', $userActionPlanAdvice->id)
                ->update([
                    'user_action_plan_advisable_id' => $userActionPlanAdvice->measure_application_id,
                    'user_action_plan_advisable_type' => \App\Models\MeasureApplication::class
                ]);
        }

        // and drop the measure_application_ids
        Schema::table('user_action_plan_advices', function (Blueprint $table) {
//            $table->dropForeign(['measure_application_id']);
            $table->dropColumn('measure_application_id');
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
//            $table->dropMorphs('measurable');
        });
    }
}
