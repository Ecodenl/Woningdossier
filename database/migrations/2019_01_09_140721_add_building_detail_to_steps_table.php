<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBuildingDetailToStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // collect the data we need
        $allSteps = \App\Models\Step::all();
        $allCooperation = \App\Models\Cooperation::all();
        $buildingDetailStepNameUuid = \App\Helpers\Str::uuid();

        // increment the step order
        foreach ($allSteps as $i => $step) {
            $i++;
            $step->order = $i;
            $step->save();
        }

        // now for every cooperation we update de step order as well
        foreach ($allCooperation as $cooperation) {
            foreach ($allSteps as $i =>  $step) {
                $i++;
                // get the cooperation steps query
                $cooperationStepsQuery = $cooperation->steps();
                // now find the selected step
                $cooperationStep = $cooperationStepsQuery->find($step->id);
                if ($cooperationStep instanceof \App\Models\Step) {
                    // update the pivot table / cooperation_step
                    $cooperationStepsQuery->updateExistingPivot($cooperationStep->id, ['order' => $i]);
                }
            }

        }

        // data for the new step
        $buildingDetailStep = [
            'slug' => 'building-detail',
            'name' => $buildingDetailStepNameUuid,
            'order' => 0,
        ];

        $buildingDetailStepTranslation = [
            'language' => 'nl',
            'key' => $buildingDetailStepNameUuid,
            'translation' => 'Woning details',
        ];

        // create it
        // connecting will be handled through the observer
        \App\Models\Step::create($buildingDetailStep);
        \App\Models\Translation::create($buildingDetailStepTranslation);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // collect the data we need
        $allSteps = \App\Models\Step::all();
        $allCooperation = \App\Models\Cooperation::all();

        // increment the step order
        foreach ($allSteps as $i => $step) {
            $step->order = $i;
            $step->save();
        }

        // now for every cooperation we update de step order as well
        foreach ($allCooperation as $cooperation) {
            foreach ($allSteps as $i =>  $step) {
                // get the cooperation steps query
                $cooperationStepsQuery = $cooperation->steps();
                // now find the selected step
                $cooperationStep = $cooperationStepsQuery->find($step->id);
                if ($cooperationStep instanceof \App\Models\Step) {
                    // update the pivot table / cooperation_step
                    $cooperationStepsQuery->updateExistingPivot($cooperationStep->id, ['order' => $i]);
                }
            }

        }


        // get the added step
        $stepQuery = DB::table('steps')->where('slug', 'building-detail');
        // remove the translations
        DB::table('translations')->where('key', $stepQuery->first()->name)->delete();
        // remove the step from all the cooperations
        DB::table('cooperation_steps')->where('step_id', $stepQuery->first()->id)->delete();
        // delete the step itself
        $stepQuery->delete();
    }
}
