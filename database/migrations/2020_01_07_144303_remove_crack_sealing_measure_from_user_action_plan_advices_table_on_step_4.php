<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCrackSealingMeasureFromUserActionPlanAdvicesTableOnStep4 extends Migration
{
    use \App\Traits\DebugableMigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $insulatedGlazing = DB::table('steps')->where('short', 'insulated-glazing')->first();
        $ventilation = DB::table('steps')->where('short', 'ventilation')->first();

        $crackSealing = DB::table('measure_applications')->where('short', 'crack-sealing')->first();

        if ($insulatedGlazing instanceOf stdClass && $crackSealing instanceof stdClass) {
            $userActionPlanAdvices = DB::table('user_action_plan_advices')
                ->where('step_id', $insulatedGlazing->id)
                ->where('measure_application_id', $crackSealing->id)
                ->get();

            foreach ($userActionPlanAdvices as $actionPlanAdvice) {

                $this->line("-------------");
                $this->line("migrating: action plan advice: {$actionPlanAdvice->id}");
                $this->line("migrating: user_id: {$actionPlanAdvice->user_id}");

                $crackSealingFinishedOnVentilation = DB::table('user_action_plan_advices')
                        ->where('user_id', $actionPlanAdvice->user_id)
                        ->where('measure_application_id', $crackSealing->id)
                        ->where('step_id', $ventilation->id)->first() instanceof stdClass;

                // check if the crack sealing is already finished on the ventilation page
                // if so, delete the crack sealing from the insulated glazing
                // else, update it to the ventilation step id
                if ($crackSealingFinishedOnVentilation) {
                    $this->line("migrating: user already finished crack sealing on ventilation");
                    $this->line("migrating: deleting the old action plan advice");
                    DB::table('user_action_plan_advices')
                        ->where('id', $actionPlanAdvice->id)
                        ->delete();
                } else {
                    $this->line("migrating: user did not finish it, update with new step");
                    DB::table('user_action_plan_advices')
                        ->where('id', $actionPlanAdvice->id)
                        ->update([
                            'step_id' => $ventilation->id
                        ]);

                }
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
        //
    }
}
