<?php

namespace App\Services;

use App\Helpers\DataTypes\Caster;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\ExampleBuilding;
use App\Models\ExampleBuildingContent;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\User;
use Doctrine\DBAL\Exception;
use Illuminate\Support\Facades\Log;

class ExampleBuildingService
{
    const NEVER_OVERWRITE_TOOL_QUESTION_SHORTS = [
        'building-type-category',
        // 'building-type',
        'building-contract-type',
        'build-year',
        'specific-example-building',
        'surface',
    ];

    const SOURCE_FROM_EXTERN_TOOL_QUESTION_SHORTS = [
        'energy-label',
    ];


    /**
     * Apply an example building on the given building.
     *
     * @param int $buildYear Build year for selecting the appropriate example building content
     * @param Building $building Target building to apply to
     */
    public static function apply(ExampleBuilding $exampleBuilding, int $buildYear, Building $building, InputSource $inputSource): void
    {
        // used to check if there is a answer available.
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // Clear the current example building data
        self::log('Lookup ' . $exampleBuilding->getTranslation('name', 'nl') . ' for ' . $buildYear . " (" . $inputSource->name . ") building id {$building->id}");
        $contents = $exampleBuilding->getContentForYear($buildYear);

        if (! $contents instanceof ExampleBuildingContent) {
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

            // Some building types do not have a generic example building
            if ($genericExampleBuilding instanceof ExampleBuilding) {
                self::log("Example building is specific. Generic counterpart is " . $genericExampleBuilding->getTranslation('name', 'nl'));

                $genericContent = $genericExampleBuilding->getContentForYear($buildYear);

                if ($genericContent instanceof ExampleBuildingContent) {
                    self::log("We merge the contents");
                    $exampleData = array_replace_recursive($exampleData, $genericContent->content);
                }
            }
        }


        self::log(
            'Applying Example Building ' . $exampleBuilding->getTranslation('name', 'nl') . ' (' . $exampleBuilding->id . ', ' . $contents->build_year . ') for input source ' . $inputSource->name
        );


        // There are some fixed tool questions which are NOT allowed to be overwritten by the example building
        // we collect them here, and possibly overwrite the example building data if the user his answer is not null
        $fixedToolQuestionShorts = array_merge(ToolQuestionHelper::SUPPORTED_API_SHORTS, static::NEVER_OVERWRITE_TOOL_QUESTION_SHORTS);
        foreach ($fixedToolQuestionShorts as $toolQuestionShort) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

            if ($inputSource->short !== InputSource::EXAMPLE_BUILDING_SHORT) {
                $answer = $building->getAnswer($inputSource, $toolQuestion);
                if (! is_null($answer)) {
                    // You may ask yourself; why not just unset the question ?
                    // excellent question!
                    // The clearExampleBuilding method will remove all the user its input
                    // so the key here is to overwrite the exampleData, this way it will be saved again.
                    $exampleData[$toolQuestionShort] = $answer;
                }
            }
        }

        // There are also some tool questions that may be filled externally. The data from external is prioritized.
        foreach (static::SOURCE_FROM_EXTERN_TOOL_QUESTION_SHORTS as $toolQuestionShort) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

            $answer = $building->getAnswer(InputSource::external(), $toolQuestion);
            if (! is_null($answer)) {
                $exampleData[$toolQuestionShort] = $answer;
            }
        }

        // We already checked the non overwritteable tool questions
        // now its safe to clear it.
        self::clearBuilding($building, $inputSource);

        $toolQuestionService = ToolQuestionService::init()
            ->building($building)
            ->currentInputSource($inputSource);
        foreach ($exampleData as $toolQuestionShort => $value) {
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
            $shouldSave = true;

            // a relationship that is not set in the EB, we wont save it.
            if ($toolQuestion->data_type === Caster::IDENTIFIER && is_null($value)) {
                $shouldSave = false;
            }

            if ($shouldSave) {
                $toolQuestionService->toolQuestion($toolQuestion)
                    ->save($value);
            }
        }


        // the building type is a part of the example building, not the example building its content.
        $toolQuestionService->toolQuestion(ToolQuestion::findByShort('building-type'))
            ->save($exampleBuilding->building_type_id);
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
