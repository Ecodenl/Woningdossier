<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultUserInterestsForCompletedSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $completedStepsWithoutInterest = DB::table('completed_steps as cs')
            ->select('cs.input_source_id', 'cs.step_id', 'cs.building_id', 'b.user_id', 'ui.interested_in_type', 'ui.interested_in_id', 'ui.interest_id')
            ->leftJoin('buildings as b', 'cs.building_id', '=', 'b.id')
            ->leftJoin('user_interests as ui', function ($join) {
                $join->on('ui.user_id', '=', 'b.user_id')
                    ->whereRaw('ui.input_source_id = cs.input_source_id')
                    ->whereRaw('ui.interested_in_id = cs.step_id');
            })->whereNull('ui.interested_in_type')
            ->whereNotIn('step_id', [1, 12, 13, 14, 15])
            ->whereNull('b.deleted_at')
            ->get();

        $defaultInterest = \App\Models\Interest::where('calculate_value', 1)->first();
        foreach ($completedStepsWithoutInterest as $completedStepWithoutInterest) {
            DB::table('user_interests')
                ->insert([
                    'user_id' => $completedStepWithoutInterest->user_id,
                    'input_source_id' => $completedStepWithoutInterest->input_source_id,
                    'interested_in_id' => $completedStepWithoutInterest->step_id,
                    'interested_in_type' => \App\Models\Step::class,
                    'interest_id' => $defaultInterest->id,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]);
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
