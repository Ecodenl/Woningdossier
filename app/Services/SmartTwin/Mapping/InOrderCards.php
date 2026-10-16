<?php

namespace App\Services\SmartTwin\Mapping;

use App\Models\Building;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\ElementScope;
use App\Services\SmartTwin\Mapping\Elements\RoofShape;
use App\Services\SmartTwin\Mapping\Mappers\SolutionMeasures;

/**
 * Which "In orde" cards an import adds: one per element that is in good shape and that SmartTwin
 * does not advise anything for.
 *
 * SmartTwin says nothing about what is in order; Hoomdossier judges it from the current situation
 * the import just wrote, as it always did for its own woonplan (stuurgroep). "Good" is Goede
 * isolatie or better (ElementValue::countsAsGood(), Patrick 14 October 2026). An element SmartTwin
 * advises a measure for is not in order, whatever its current state.
 *
 * The measure per element was decided on 13 October (V2b): facade by whether it is a cavity wall,
 * floor, and the roof per shape. Glass follows once its mapping exists.
 */
class InOrderCards
{
    /** has-cavity-wall: yes. See WallInsulationHelper::getCavityWallValues(). */
    private const CAVITY_WALL_YES = 1;

    public function __construct(private readonly SolutionMeasures $measures)
    {
    }

    /**
     * @param  array<int, string>  $advisedMeasureShorts  The measures the package advises.
     * @return array<int, string> The measure shorts to put in "In orde".
     */
    public function for(Building $building, array $advisedMeasureShorts): array
    {
        $scopes = [];
        foreach ($advisedMeasureShorts as $measureShort) {
            array_push($scopes, ...ElementScope::forMeasure($measureShort, $this->measures->stepFor($measureShort)));
        }

        $advised = function (Element $element, ?RoofShape $shape = null) use ($scopes): bool {
            foreach ($scopes as $scope) {
                if ($scope->covers($element, $shape)) {
                    return true;
                }
            }

            return false;
        };

        $cards = [];

        if (! $advised(Element::FACADE) && $this->isGood($building, 'current-wall-insulation')) {
            $cards[] = self::CAVITY_WALL_YES === (int) $this->answer($building, 'has-cavity-wall')
                ? 'cavity-wall-insulation'
                : 'facade-wall-insulation';
        }

        if (! $advised(Element::FLOOR) && $this->isGood($building, 'current-floor-insulation')) {
            $cards[] = 'floor-insulation';
        }

        foreach (RoofShape::cases() as $shape) {
            // Only a shape the dwelling has: a removed shape has no answer.
            if (! $advised(Element::ROOF, $shape) && $this->isGood($building, "is-{$shape->value}-roof-insulated")) {
                $cards[] = RoofShape::FLAT === $shape ? 'roof-insulation-flat-current' : 'roof-insulation-pitched-inside';
            }
        }

        return $cards;
    }

    private function isGood(Building $building, string $toolQuestionShort): bool
    {
        $elementValue = ElementValue::find($this->answer($building, $toolQuestionShort));

        return $elementValue instanceof ElementValue && $elementValue->countsAsGood();
    }

    private function answer(Building $building, string $toolQuestionShort): mixed
    {
        $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

        return $toolQuestion instanceof ToolQuestion
            ? $building->getAnswer(InputSource::master(), $toolQuestion)
            : null;
    }
}
