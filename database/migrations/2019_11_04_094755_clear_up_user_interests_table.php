<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClearUpUserInterestsTable extends Migration
{
    use \App\Traits\DebugableMigrationTrait;

    /**
     * Run the migrations.
     *
     * Migration to clean up the table from duplicate answers and such
     *
     * @return void
     */
    public function up()
    {
        $this->cleanUpUserInterestDuplicates();
    }

    private function cleanUpUserInterestDuplicates()
    {
        // get all the duplicates
        $userInterestsWithDuplicates = \DB::table('user_interests')
            ->select('user_id', 'input_source_id', 'interested_in_type', 'interested_in_id', \DB::raw('count(interested_in_id)'))
            ->groupBy('user_id', 'input_source_id', 'interested_in_id', 'interested_in_type')
            ->having('count(interested_in_id)', '>', 1)
            ->get();


        foreach ($userInterestsWithDuplicates as $userInterestsWithDuplicate) {
            $this->line('duplicate user interest for user_id :'.$userInterestsWithDuplicate->user_id);

            // get the duplicate records
            $duplicateUserInterests = \DB::table('user_interests')
                ->where('user_id', $userInterestsWithDuplicate->user_id)
                ->where('input_source_id', $userInterestsWithDuplicate->input_source_id)
                ->where('interested_in_type', $userInterestsWithDuplicate->interested_in_type)
                ->where('interested_in_id', $userInterestsWithDuplicate->interested_in_id)->get();

            // loop through them, count the current duplications and when they are higher than 1 delete a row
            foreach ($duplicateUserInterests as $duplicateUserInterest) {
                $currentDuplicateCount = \DB::table('user_interests')
                    ->where('user_id', $userInterestsWithDuplicate->user_id)
                    ->where('input_source_id', $userInterestsWithDuplicate->input_source_id)
                    ->where('interested_in_type', $userInterestsWithDuplicate->interested_in_type)
                    ->where('interested_in_id', $userInterestsWithDuplicate->interested_in_id)->count();

                $anyDuplicatesLeftForInterestedInId = $currentDuplicateCount > 1;
                $this->line('total duplicate count: '.$currentDuplicateCount);

                if ($anyDuplicatesLeftForInterestedInId) {
                    $this->line('removing duplicate for user_id: '.$duplicateUserInterest->user_id);
                    \DB::table('user_interests')->where('id', $duplicateUserInterest->id)->delete();
                } else {
                    $this->line('no duplicates left for user_id :'.$duplicateUserInterest->user_id);
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
