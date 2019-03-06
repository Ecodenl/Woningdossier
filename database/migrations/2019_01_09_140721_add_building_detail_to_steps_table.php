<?php

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
        $steps = \DB::table('steps')->get();
        $cooperations = \DB::table('cooperations')->get();
        $buildingDetailStepNameUuid = \App\Helpers\Str::uuid();

        // increment the step order
        foreach ($steps as $i => $step) {
            \DB::table('steps')->where('id', '=', $step->id)->update(['order' => $i + 1]);
        }

        // now for every cooperation we update de step order as well
        foreach ($cooperations as $cooperation) {
            foreach ($steps as $i => $step) {
                \DB::table('cooperation_steps')
                    ->where('step_id', '=', $step->id)
                    ->where('cooperation_id', '=', $cooperation->id)
                   ->update(['order' => $i + 1]);
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
        \DB::table('steps')->insert($buildingDetailStep);
        \DB::table('translations')->insert($buildingDetailStepTranslation);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // collect the data we need
        $cooperations = \DB::table('cooperations')->get();
        $steps = DB::table('steps')->get();

        foreach ($steps as $i => $step) {
            \DB::table('steps')->where('id', '=', $step->id)->update(['order' => $i]);
        }

        // now for every cooperation we update de step order as well
        foreach ($cooperations as $cooperation) {
            foreach ($steps as $i =>  $step) {
                \DB::table('cooperation_steps')
                    ->where('step_id', '=', $step->id)
                    ->where('cooperation_id', '=', $cooperation->id)
                   ->update(['order' => $i]);
            }
        }

        // get the added step
        $stepQuery = \DB::table('steps')->where('slug', 'building-detail')->first();
        // remove the translations
        if ($stepQuery instanceof \stdClass) {
            \DB::table('translations')->where('key',
                $stepQuery->name)->delete();
            // remove the step from all the cooperations
            \DB::table('cooperation_steps')->where('step_id',
                $stepQuery->id)->delete();
            // delete the step itself
            \DB::table('steps')->where('slug', 'building-detail')->limit(1)->delete();
        }
    }
}
