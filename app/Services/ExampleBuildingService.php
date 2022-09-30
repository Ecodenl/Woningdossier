<?php

namespace App\Services;

use App\Events\ExampleBuildingChanged;
use App\Helpers\Arr;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingService;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
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
     * @param InputSource|null $inputSource
     * @param InputSource|null $initiatingInputSource The input source starting this action.
     *
     * @return void
     */
    public static function apply(ExampleBuilding $exampleBuilding, $buildYear, Building $building, ?InputSource $inputSource = null, ?InputSource $initiatingInputSource = null)
    {
        $inputSource = $inputSource ?? InputSource::findByShort(InputSource::EXAMPLE_BUILDING);
        // unless stated differently: compare to master input values
        $initiatingInputSource = $initiatingInputSource ?? InputSource::findByShort(InputSource::MASTER_SHORT);

        // Clear the current example building data
        self::log('Lookup ' . $exampleBuilding->name . ' for ' . $buildYear . " (" . $inputSource->name . ")");
        $contents = $exampleBuilding->getContentForYear($buildYear);

        if (!$contents instanceof ExampleBuildingContent) {
            // There's nothing to apply
            self::log('No data to apply');

            return;
        }
        // used for throwing the event at the end
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $buildingFeature = $building->buildingFeatures()->forInputSource($masterInputSource)->first();
        $oldExampleBuilding = $buildingFeature->exampleBuilding;

        // traverse the contents:
        $exampleData = $contents->content;

        // new: merge-like behavior
        if ($exampleBuilding->isSpecific()) {
            $genericExampleBuilding = ExampleBuilding::generic()->where(
                'building_type_id',
                $exampleBuilding->building_type_id,
            )->first();
            self::log(
                "Example building is specific. Generic counterpart is " . $genericExampleBuilding->name
            );
            $genericContent = $genericExampleBuilding->getContentForYear(
                $buildYear
            );
            if ($genericContent instanceof ExampleBuildingContent) {
                self::log("We merge the contents");
                $exampleData = array_replace_recursive(
                    $exampleData,
                    $genericContent->content
                );
            }
        }

        self::log(
            'Applying Example Building ' . $exampleBuilding->name . ' (' . $exampleBuilding->id . ', ' . $contents->build_year . ') for input source ' . $inputSource->name
        );

        self::clearExampleBuilding($building, $inputSource);

        Log::debug($exampleBuilding);

        $fixedToolQuestionShorts = array_merge(ToolQuestionHelper::SUPPORTED_API_SHORTS, static::NEVER_OVERWRITE_TOOL_QUESTION_SHORTS);

        foreach ($exampleData as $toolQuestionShort => $value) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

            $shouldSave = true;
            // check if the tool question is a fixed one
            // a fixed on cant be overwritten by example building data unless the field is empty
            if(in_array($toolQuestionShort, $fixedToolQuestionShorts)) {
                // the tool question is fixed one, lets not save it before the last check
                $shouldSave = false;
                if (is_null($building->getAnswer($initiatingInputSource, $toolQuestion))) {
                    // the tool question answer is null, meaning we can set it.
                    $shouldSave = true;
                }
            }

            if (in_array($toolQuestionShort, ['surface', 'build-year'])) {
                if (empty($value)) {
                    $shouldSave = false;
                }
            }

            if ($shouldSave) {
                Log::debug("Saving {$toolQuestionShort}..");
                ToolQuestionService::init($toolQuestion)
                    ->building($building)
                    ->currentInputSource($inputSource)
                   ->save($value);
            } else {
                Log::debug("Skipping {$toolQuestionShort}");
            }
        }


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
