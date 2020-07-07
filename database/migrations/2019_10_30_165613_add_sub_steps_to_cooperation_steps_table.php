<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubStepsToCooperationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\App\Models\Cooperation::all() as $cooperation) {
            $subStepsToAttach = DB::table('steps')
                ->whereIn('short', [
                    'building-characteristics',
                    'current-state',
                    'usage',
                    'interest'
                ])->get();

            foreach ($subStepsToAttach as $subStepToAttach) {
                $cooperation->steps()->attach(
                    $subStepToAttach->id,
                    ['order' => $subStepToAttach->order]
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
        //
    }
}
