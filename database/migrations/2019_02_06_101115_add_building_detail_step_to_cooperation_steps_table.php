<?php

use Illuminate\Database\Migrations\Migration;

class AddBuildingDetailStepToCooperationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cooperations = \DB::table('cooperations')->get();
        $steps = \DB::table('steps')->get();

        foreach ($cooperations as $cooperation) {
            foreach ($steps as $step) {
                \DB::table('cooperation_steps')->updateOrInsert(
                    [
                        'cooperation_id' => $cooperation->id,
                        'step_id' => $step->id,
                    ],
                    [
                        'order' => $step->order,
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $cooperations = \DB::table('cooperations')->get();
        $steps = \DB::table('steps')->get();

        foreach ($cooperations as $cooperation) {
            foreach ($steps as $step) {
                \DB::table('cooperation_steps')
                    ->where('cooperation_id', '=', $cooperation->id)
                    ->where('step_id', '=', $step->id)
                    ->delete();
            }
        }
    }
}
