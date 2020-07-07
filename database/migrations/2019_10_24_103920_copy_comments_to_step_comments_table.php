<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CopyCommentsToStepCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = \App\Models\User::withoutGlobalScopes()->get();

        foreach ($users as $user) {
            $building = \DB::table('buildings')->where('user_id', $user->id)->first();
            $commentsByStep = $this->getAllCommentsByStep($user);
            foreach ($commentsByStep as $stepSlug => $comments) {
                $step = \DB::table('steps')->where('slug', $stepSlug)->first();
                foreach ($comments as $inputSourceName => $comment) {
                    $inputSource = \DB::table('input_sources')->where('name', $inputSourceName)->first();
                    \DB::table('step_comments')->insert([
                        'input_source_id' => $inputSource->id,
                        'building_id' => $building->id,
                        'step_id' => $step->id,
                        'comment' => $comment,
                    ]);
                }
            }
        }
    }

    private function getAllCommentsByStep($user)
    {
        $building = $user->building;

        if (!$building instanceof \App\Models\Building) {
            return [];
        }

        $allInputForMe = collect();
        $commentsByStep = [];


        /* General-data */
        $userEnergyHabitForMe = \App\Models\UserEnergyHabit::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('general-data', $userEnergyHabitForMe);

        /* wall insulation */
        $buildingFeaturesForMe = \App\Models\BuildingFeature::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('wall-insulation', $buildingFeaturesForMe);

        /* floor insualtion */
        $crawlspace = \App\Models\Element::where('short', 'crawlspace')->first();
        $buildingElementsForMe = \App\Models\BuildingElement::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('floor-insulation', $buildingElementsForMe->where('element_id', $crawlspace->id));

        /* beglazing */
        $insulatedGlazingsForMe = $building->currentInsulatedGlazing()->forMe($user)->with('inputSource')->get();
        $allInputForMe->put('insulated-glazing', $insulatedGlazingsForMe);

        /* roof */
        $currentRoofTypesForMe = $building->roofTypes()->with('roofType')->forMe($user)->with('inputSource')->get();
        $allInputForMe->put('roof-insulation', $currentRoofTypesForMe);

        /* hr boiler ketel */
        $boiler = \App\Models\Service::where('short', 'boiler')->first();
        $installedBoilerForMe = $building->buildingServices()->forMe($user)->where('service_id', $boiler->id)->with('inputSource')->get();
        $allInputForMe->put('high-efficiency-boiler', $installedBoilerForMe);

        /* sun panel*/
        $buildingPvPanelForMe = \App\Models\BuildingPvPanel::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('solar-panels', $buildingPvPanelForMe);

        /* heater */
        $buildingHeaterForMe = \App\Models\BuildingHeater::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('heater', $buildingHeaterForMe);

        /* my plan */
//        $allInputForMe->put('my-plan', UserActionPlanAdviceComments::forMe($user)->get());

        // the attributes that can contain any sort of comments.
        $possibleAttributes = ['comment', 'additional_info', 'living_situation_extra'];

//        dd($allInputForMe);
        foreach ($allInputForMe as $step => $inputForMeByInputSource) {
            foreach ($inputForMeByInputSource as $inputForMe) {
                // check if we need the extra column to extract the comment from.
                if (is_array($inputForMe->extra) && array_key_exists('comment', $inputForMe->extra)) {
                    // get the comment fields, and filter out the empty ones.
                    $inputToFilter = $inputForMe->extra;
                } else {
                    $inputToFilter = $inputForMe->getAttributes();
                }

                $inputWithComments = \Illuminate\Support\Arr::only($inputToFilter, $possibleAttributes);

                $comments = array_values(false ? $inputWithComments : array_filter(
                    $inputWithComments
                ));

                // if the comments are not empty, add it to the array with its input source
                // only add the comment, not the key / column name.
                if (! empty($comments)) {
                    $commentsByStep[$step][$inputForMe->inputSource->name] = $comments[0];
                }
            }
        }

        return $commentsByStep;
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
