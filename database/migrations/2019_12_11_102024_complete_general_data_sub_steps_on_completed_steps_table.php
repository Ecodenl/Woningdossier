<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompleteGeneralDataSubStepsOnCompletedStepsTable extends Migration
{
    use \App\Traits\DebugableMigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $counter = 0;

        $generalData = DB::table('steps')->where('short', 'general-data')->first();

        if ($generalData instanceof stdClass) {


            $generalDataSubSteps = DB::table('steps')->where('parent_id', $generalData->id)->get();

            $completedGeneralDataSteps = DB::table('completed_steps')
                ->where('step_id', $generalData->id)
                ->get();

            foreach ($completedGeneralDataSteps as $completedGeneralDataStep) {

                foreach ($generalDataSubSteps as $subStep) {
                    $counter++;

                    DB::table('completed_steps')->insert([
                        'step_id' => $subStep->id,
                        'building_id' => $completedGeneralDataStep->building_id,
                        'input_source_id' => $completedGeneralDataStep->input_source_id,
                    ]);
                }
            }
            $this->line("A total of {$counter} completed steps have been added");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
            “Once you are at the point where you can call yourself a writer, there’s no turning back.”
            ― A.D. Posey
        */
    }
}
