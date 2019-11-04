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
        $userInterests = DB::table('user_interests')
            ->where('interested_in_type', '!=', 'measure_application')
            ->get();

        foreach ($userInterests as $userInterest) {
            $this->line('-------------------------------------------------');
            $this->line('migrating data for user_id: '.$userInterest->user_id);
            $this->line('migrating data for input_source_id: '.$userInterest->input_source_id);
            $this->line('old interested_in_type: '.$userInterest->interested_in_type);
            $newInterestedInId = $this->getInterestedInIdForCurrentInterest($userInterest);
            $this->line('new interested_in_id: '.$newInterestedInId);

            DB::table('user_interests')
                ->where('id', $userInterest->id)
                ->update([
                    'interested_in_type' => \App\Models\Step::class,
                    'interested_in_id' => $newInterestedInId
                ]);
        }
dd('ok');
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
                // even though the services itself does not exist anymore
                // the user interests are still stored
                1 => 'heat-pump',
                2 => 'heat-pump',
                // new service id for the heat pump
                8 => 'heat-pump',
                4 => 'high-efficiency-boiler',
                7 => 'solar-panels',
                3 => 'heater',
                6 => 'ventilation-information',
            ],
        ];

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
