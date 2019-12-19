<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CopyGeneralDataCommentToUsageStepOnStepCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $usageStep = DB::table('steps')->where('short', 'usage')->first();
        $generalDataStep = DB::table('steps')->where('short', 'general-data')->first();

        if ($usageStep instanceof stdClass) {
            DB::table('step_comments')
                ->where('step_id', $generalDataStep->id)
                ->update([
                    'step_id' => $usageStep->id,
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
