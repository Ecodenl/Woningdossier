<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserInterestFromExampleBuildingContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * here we will remove the user_interest values from the content of a example building
     *
     * @return void
     */
    public function up()
    {

        $exampleBuildingContents = \DB::table('example_building_contents')->get();

        foreach ($exampleBuildingContents as $exampleBuildingContent) {
            $ebContent = json_decode($exampleBuildingContent->content, true);
            unset($ebContent['general-data']['user_interest']);

            \DB::table('example_building_contents')
                ->where('id', $exampleBuildingContent->id)
                ->update([
                    'content' => json_encode($ebContent)
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
