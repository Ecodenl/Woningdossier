<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateInterestedInTypesToModelNamesOnUserInterestsTable extends Migration
{
    use \App\Traits\DebugableMigrationTrait;

    /**
     * Run the migrations.
     *
     * Migration to migrate the interested_in_type to model names and the interested_in_ids to the matching step ids
     *
     * @return void
     */
    public function up()
    {

        $userInterests = DB::table('user_interests')->get();

        foreach ($userInterests as $userInterest) {
            $this->line('-------------------------------------------------');
            $this->line('migrating data for user_id: '.$userInterest->user_id);
            $this->line('migrating data for input_source_id: '.$userInterest->input_source_id);
            $this->line('old interested_in_type: '.$userInterest->interested_in_type);
            $newInterestedInType = $this->determineInterestedInType($userInterest);


            $updateData = ['interested_in_type' => $newInterestedInType];

            if ($userInterest->interested_in_type != 'measure_application') {
                $newInterestedInId = $this->getInterestedInIdForCurrentInterest($userInterest);
                $this->line('new interested_in_id: '.$newInterestedInId);

                $updateData['interested_in_id'] = $newInterestedInId;
            }
            DB::table('user_interests')
                ->where('id', $userInterest->id)
                ->update($updateData);
        }
    }


    private function determineInterestedInType($userInterest)
    {
        return in_array($userInterest->interested_in_type, ['element', 'service']) ? \App\Models\Step::class : \App\Models\MeasureApplication::class;
    }

    private function getInterestedInIdForCurrentInterest($userInterest)
    {
        $elementAndServiceIdToStepShort = [
            'element' => [
                1 => 'insulated-glazing',
                2 => 'insulated-glazing',
                3 => 'wall-insulation',
                4 => 'floor-insulation',
                5 => 'roof-insulation'
            ],
            'service' => [
                3 => 'heater',
                4 => 'high-efficiency-boiler',
                6 => 'ventilation-information',
                7 => 'solar-panels',
                8 => 'heat-pump',
            ],
        ];

        if ($userInterest->interested_in_type == 'service' & in_array($userInterest->interested_in_id, [1,2])){
            $userInterest->interested_in_id = 8;
        }

        $stepShort = $elementAndServiceIdToStepShort[$userInterest->interested_in_type][$userInterest->interested_in_id];
        $this->line('step for the interested_in_id: '.$stepShort. ' based on (old) interested_in_id: '.$userInterest->interested_in_id);

        $step = DB::table('steps')->where('short', $stepShort)->first();

        return $step->id;
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
