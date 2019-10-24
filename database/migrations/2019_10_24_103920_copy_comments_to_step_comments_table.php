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
            $commentsByStep = \App\Helpers\StepHelper::getAllCommentsByStep($user);
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
