<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateExistingEbContentToNewToolStructureOnExampleBuildingContentsTable extends Migration
{
    use \App\Traits\DebugableMigrationTrait;

    /**
     * Run the migrations.
     *
     * This migration will handle the new content structure of the tool.
     *
     * moving the element questions that belong to on the current state page to that step
     * moving all the data into substeps, when no substep exist add it to a empty substep
     * renaming hr-boiler.service.boilerid.extra.year to hr-boiler.service.boilerid.extra.date
     *
     * elements and services
     *
     * @return void
     */
    public function up()
    {

        $exampleBuildingContents = \DB::table('example_building_contents')->get();

        $boiler = \DB::table('services')->where('short', 'boiler')->first();
        $elementsThatBelongOnCurrentStatePage = \DB::table('elements')->whereIn('short', [
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
                        if ($stepSlug == 'high-efficiency-boiler') {

                            // can be a deeper array or the year itself.
                            $boilerExtra = $stepData['service'][$boiler->id]['extra'];

                            if (is_array($boilerExtra)) {
                                $content[$stepSlug]['service'][$boiler->id]['extra']['date'] = $stepData['service'][$boiler->id]['extra']['year'];
                                unset($content[$stepSlug]['service'][$boiler->id]['extra']['year']);
                            } else {
                                // we first have to unset the key, otherwise we cant assign new deeper values
                                unset($content[$stepSlug]['service'][$boiler->id]['extra']);
                                $content[$stepSlug]['service'][$boiler->id]['extra']['date'] = $boilerExtra;
                            }

                        } else if ($stepSlug == 'heater') {
                            $generalDataServiceData = $generalDataServiceData + $stepData['service'];
                            unset($content[$stepSlug]['service']);
                        }

                    }

                    // remove it, wont be possible to store anyways.
                    if (array_key_exists('user_interests', $stepData)) {
                        unset($content[$stepSlug]['user_interests']);
                    }

                    // now we will move the data into an empty sub step, just for consistency.
                    $content[$stepSlug]['-'] = array_splice($content[$stepSlug], 0);
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
