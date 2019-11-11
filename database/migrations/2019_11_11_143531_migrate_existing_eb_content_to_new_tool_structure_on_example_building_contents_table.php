<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateExistingEbContentToNewToolStructureOnExampleBuildingContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will handle the new content structure of the tool.
     *
     * elements and services
     *
     * @return void
     */
    public function up()
    {
        $exampleBuildingContents = \DB::table('example_building_contents')->get();

        $elementsThatBelongOnCurrentStatePage = DB::table('elements')->whereIn('short', [
            'sleeping-rooms-windows', 'living-rooms-windows', 'crack-sealing',
            'wall-insulation', 'floor-insulation', 'roof-insulation'
        ])->get()->pluck('id')->toArray();

        foreach ($exampleBuildingContents as $exampleBuildingContent) {
            $content = json_decode($exampleBuildingContent->content, true);

            $generalDataElementData = [];
            $generalDataServiceData = [];

            // handle the elements and services.
            foreach ($content as $stepSlug => $stepData) {
                if ($stepSlug == "general-data") {
                    $generalDataElementData = $content['general-data']['element'] ?? [];
                    $generalDataServiceData = $content['general-data']['service'] ?? [];
                } else {
                    // as long as the keys dont overlap its all goood
                    if (array_key_exists('element', $stepData)) {

                        $idsToCheck = [];
                        // since the element contains non numeric values we cant array flip, so we have to do this
                        foreach ($stepData['element'] as $elementId => $elementValue) {
                            $idsToCheck[] = $elementId;
                        }

                        // now we have all the ids, we can intersect it and loop over it to get the right values.
                        $elementsToMigrate = [];
                        // intersect the arrays to only return the elements that belong on the current state page itself
                        foreach (array_intersect($idsToCheck, $elementsThatBelongOnCurrentStatePage) as $elementId) {
                            $elementsToMigrate[$elementId] = $stepData['element'][$elementId];
                        }

                        $generalDataElementData = $generalDataElementData + $elementsToMigrate;
                        unset($content[$stepSlug]['element']);
                    }
                    if (array_key_exists('service', $stepData)) {

                        $generalDataServiceData = $generalDataServiceData + $stepData['service'];
                        unset($content[$stepSlug]['service']);
                    }

                    // remove it, wont be possible to store anyways.
                    if (array_key_exists('user_interests', $stepData)) {
                        unset($content[$stepSlug]['user_interests']);
                    }
                }
            }

            // assign the data to the new steps
            if (array_key_exists('building_features', $content['general-data'])) {
                $content['general-data']['building-characteristics']['building_features'] = $content['general-data']['building_features'];
            }
            if (array_key_exists('user_energy_habits', $content['general-data'])) {
                $content['general-data']['usage']['user_energy_habits'] = $content['general-data']['user_energy_habits'];
            }
            $content['general-data']['current-state']['element'] = $generalDataElementData;
            $content['general-data']['current-state']['service'] = $generalDataServiceData;

            // unset the old keys
            unset($content['general-data']['element'], $content['general-data']['service'], $content['general-data']['building_features'], $content['general-data']['user_energy_habits']);

            \DB::table('example_building_contents')
                ->where('id', $exampleBuildingContent->id)
                ->update([
                    'content' => json_encode($content)
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
