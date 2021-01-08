<?php

use Illuminate\Database\Migrations\Migration;

class ChangeColumnNameInExampleBuildingContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // previous the insulated_glazing_id was used in the example building content
        // however, this did not match the column name in the table
        // so this migration will rename it from insulated_glazing_id to insulating_glazing_id;
        $measureApplicationShorts = [
            'hrpp-glass-only',
            'hrpp-glass-frames',
            'hr3p-frames',
            'glass-in-lead',
        ];

        $measureApplications = DB::table('measure_applications')
            ->whereIn('short', $measureApplicationShorts)
            ->get()
            ->pluck('id')
            ->toArray();

        $exampleBuildingContents = DB::table('example_building_contents')->get();

        foreach ($exampleBuildingContents as $exampleBuildingContent) {
            $baseContent = json_decode($exampleBuildingContent->content, true);
            $buildingInsulatedGlazingContent = $baseContent['insulated-glazing']['-']['building_insulated_glazings'];
            foreach ($measureApplications as $measureApplicationId) {
                if (array_key_exists('insulated_glazing_id', $buildingInsulatedGlazingContent[$measureApplicationId])) {
                    $baseContent['insulated-glazing']['-']['building_insulated_glazings'][$measureApplicationId]['insulating_glazing_id']
                        = $buildingInsulatedGlazingContent[$measureApplicationId]['insulated_glazing_id'];

                    // and unset the old key
                    unset($baseContent['insulated-glazing']['-']['building_insulated_glazings'][$measureApplicationId]['insulated_glazing_id']);
                }
            }

            DB::table('example_building_contents')
                ->where('id', $exampleBuildingContent->id)
                ->update([
                    'content' => json_encode($baseContent),
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
    }
}
