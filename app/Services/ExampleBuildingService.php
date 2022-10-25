<?php

namespace App\Services;

use App\Events\ExampleBuildingChanged;
use App\Helpers\DataTypes\Caster;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ExampleBuildingService
{
    const NEVER_OVERWRITE_TOOL_QUESTION_SHORTS = [
        'building-type-category',
        // 'building-type',
        'build-year',
        'specific-example-building',
        'surface',
    ];

    /**
     * Apply an example building on the given building.
     *
     * @param ExampleBuilding $exampleBuilding
     * @param int $buildYear Build year for selecting the appropriate example building content
     * @param Building $building Target building to apply to
     *
     * @return void
     */
    public static function apply(ExampleBuilding $exampleBuilding, $buildYear, Building $building, InputSource $inputSource)
    {
        // used to check if there is a answer available.
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // Clear the current example building data
        self::log('Lookup ' . $exampleBuilding->name . ' for ' . $buildYear . " (" . $inputSource->name . ") building id {$building->id}");
        $contents = $exampleBuilding->getContentForYear($buildYear);

        if (!$contents instanceof ExampleBuildingContent) {
            // There's nothing to apply
            self::log('No data to apply');

            return;
        }
        // used for throwing the event at the end
        $buildingFeature = $building->buildingFeatures()->forInputSource($masterInputSource)->first();
        $oldExampleBuilding = $buildingFeature->exampleBuilding;

        // traverse the contents:
        $exampleData = $contents->content;

        // new: merge-like behavior
        if ($exampleBuilding->isSpecific()) {
            $genericExampleBuilding = ExampleBuilding::generic()
                ->where('building_type_id', $exampleBuilding->building_type_id)
                ->first();
            self::log("Example building is specific. Generic counterpart is " . $genericExampleBuilding->name);

            $genericContent = $genericExampleBuilding->getContentForYear($buildYear);

            if ($genericContent instanceof ExampleBuildingContent) {
                self::log("We merge the contents");
                $exampleData = array_replace_recursive($exampleData, $genericContent->content);
            }
        }


        self::log(
            'Applying Example Building ' . $exampleBuilding->name . ' (' . $exampleBuilding->id . ', ' . $contents->build_year . ') for input source ' . $inputSource->name
        );


        // there are some fixed tool questions which are now allowed to be overwritten by the example building
        // we collect them here, and possibly overwrite the example building data if the user his answer is not null
        $fixedToolQuestionShorts = array_merge(ToolQuestionHelper::SUPPORTED_API_SHORTS, static::NEVER_OVERWRITE_TOOL_QUESTION_SHORTS);
        foreach ($fixedToolQuestionShorts as $toolQuestionShort) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

            if ($toolQuestion->short !== InputSource::EXAMPLE_BUILDING && in_array($toolQuestionShort, $fixedToolQuestionShorts)) {

                $answer = $building->getAnswer($inputSource, $toolQuestion);
                if (!is_null($answer)) {
                    // You may ask yourself; why not just unset the question ?
                    // excellent question!
                    // The clearExampleBuilding method will remove all the user its input
                    // so the key here is to overwrite the exampleData, this way it will be saved again.
                    $exampleData[$toolQuestionShort] = $answer;
                }
            }
        }

        // We already checked the non overwritteable tool questions
        // now its safe to clear it.
        self::clearBuilding($building, $inputSource);


        Log::debug($exampleBuilding);

        // basically; tool questions that can only be updated when the user his own filled in answers are empty
        $fixedToolQuestionShorts = array_merge(ToolQuestionHelper::SUPPORTED_API_SHORTS, static::NEVER_OVERWRITE_TOOL_QUESTION_SHORTS);

        foreach ($exampleData as $toolQuestionShort => $value) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
            $shouldSave = true;

            // check if the tool question is a fixed one
            // a fixed on can't be overwritten by example building data unless the field is empty
            // AND unless its a example building, the data from the example building input source can always be overwritten.
            if ($toolQuestion->short !== InputSource::EXAMPLE_BUILDING && in_array($toolQuestionShort, $fixedToolQuestionShorts)) {
                // the tool question is fixed one, lets not save it before the last check
                $shouldSave = false;
                // now check if the user has already answered the question with a non null value
                if (is_null($building->getAnswer($inputSource, $toolQuestion))) {
                    // the tool question answer is null, meaning we can update it with the exampel building value
                    $shouldSave = true;
                }
            }

            // a relationship that is not set in the EB, we wont save it.
            if ($toolQuestion->data_type === Caster::IDENTIFIER && is_null($value)) {
                $shouldSave = false;
            }

            if ($shouldSave) {
                ToolQuestionService::init($toolQuestion)
                    ->building($building)
                    ->currentInputSource($inputSource)
                    ->save($value);
            } else {
//                Log::debug("Skipping {$toolQuestionShort}");
            }
        }


        // the building type is a part of the example building, not the example building its content.
        ToolQuestionService::init(ToolQuestion::findByShort('building-type'))
            ->building($building)
            ->currentInputSource($inputSource)
            ->save($exampleBuilding->building_type_id);


        ExampleBuildingChanged::dispatch(
            $building,
            $oldExampleBuilding,
            $exampleBuilding
        );
    }

    public static function clearBuilding(Building $building, InputSource $inputSource)
    {
        Log::debug("Clearing example building for input source " . $inputSource->short);

        // Delete all building elements
        $building->buildingElements()->forInputSource($inputSource)->delete();
        $building->buildingFeatures()->forInputSource($inputSource)->delete();

        $building->buildingServices()->forInputSource($inputSource)->delete();
        $building->currentInsulatedGlazing()->forInputSource($inputSource)->delete();

        $roofTypesToDelete = $building->roofTypes()->forInputSource($inputSource)->get();
        foreach ($roofTypesToDelete as $roofTypeToDelete) {
            // Manually delete these so the master input source updates with it
            $roofTypeToDelete->delete();
        }

        $building->currentPaintworkStatus()->forInputSource($inputSource)->delete();
        $building->pvPanels()->forInputSource($inputSource)->delete();
        $building->heater()->forInputSource($inputSource)->delete();
        $building->toolQuestionAnswers()->forInputSource($inputSource)->delete();
        if ($building->user instanceof User) {
            // remove interests
            $building->user->userInterests()->forInputSource($inputSource)->delete();
            // remove energy habits
            $building->user->energyHabit()->forInputSource($inputSource)->delete();
        }

        return true;
    }

    protected static function log($text)
    {
        Log::debug(__CLASS__ . ' ' . $text);
    }
}
