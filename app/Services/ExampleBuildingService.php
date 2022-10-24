<?php

namespace App\Services;

use App\Events\ExampleBuildingChanged;
use App\Helpers\DataTypes\Caster;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Log;

class ExampleBuildingService
{
    const NEVER_OVERWRITE_TOOL_QUESTION_SHORTS = [
        'build-year',
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

        // The building-type-category wont be set by the exampel building
        // this is filled in before the user applied the example building
        $buildingTypeCategory = ToolQuestion::findByShort('building-type-category');
        $buildingTypeCategoryAnswer = $building->getAnswer($inputSource, $buildingTypeCategory);
        // because the eb can be applied for multiple input sources
        // assuming a answer exists, could mess up code further on the line.
        // so that is why we check whether there was a answer in the first place.
        $shouldReSaveAnswer = false;
        if (!empty($buildingTypeCategoryAnswer)) {
            $shouldReSaveAnswer = true;
        }
        // now clear it
        self::clearExampleBuilding($building, $inputSource);

        if ($shouldReSaveAnswer) {
            // and set it
            ToolQuestionService::init($buildingTypeCategory)
                ->currentInputSource($inputSource)
                ->building($building)
                ->save($buildingTypeCategoryAnswer);
        }

        Log::debug($exampleBuilding);

        // basically; tool questions that can only be updated when the user his own filled in answers are empty
        $fixedToolQuestionShorts = array_merge(ToolQuestionHelper::SUPPORTED_API_SHORTS,
            static::NEVER_OVERWRITE_TOOL_QUESTION_SHORTS);

        foreach ($exampleData as $toolQuestionShort => $value) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
            $shouldSave = true;

            // check if the tool question is a fixed one
            // a fixed on can't be overwritten by example building data unless the field is empty
            // AND unless its a example building, the data from the example building input source can always be overwritten.
            if ($toolQuestion->short !== InputSource::EXAMPLE_BUILDING && in_array($toolQuestionShort,
                    $fixedToolQuestionShorts)
            ) {
                // the tool question is fixed one, lets not save it before the last check
                $shouldSave = false;
                // now check if the user has already answered the question with a non null value
                if (is_null($building->getAnswer($masterInputSource, $toolQuestion))) {
                    // the tool question answer is null, meaning we can update it with the exampel building value
                    $shouldSave = true;
                }
            }

            // a relationship that is not set in the EB, we wont save it.
            if ($toolQuestion->data_type === Caster::IDENTIFIER && is_null($value)) {
                $shouldSave = false;
            }

            if ($shouldSave) {
//                Log::debug("Saving {$toolQuestionShort}..");
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

    public static function clearExampleBuilding(Building $building, ?InputSource $inputSource = null)
    {
        /** @var InputSource $inputSource */
        $inputSource = $inputSource ?? InputSource::findByShort(
                InputSource::EXAMPLE_BUILDING
            );

        Log::debug("Clearing example building for input source " . $inputSource->short);

        return BuildingDataService::clearBuildingFromInputSource(
            $building,
            $inputSource
        );
    }

    protected static function log($text)
    {
        Log::debug(__CLASS__ . ' ' . $text);
    }
}
