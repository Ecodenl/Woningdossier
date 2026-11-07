<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\GlassType;
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
 * floor, and the roof per shape. Glass on 7 November (decision 2026-11-07-smarttwin-glasmapping):
 * in order from HR+ (GlassType), and the card is Hoomdossier's own measure for the glass the dwelling
 * has, or none.
 */
class InOrderCards
{
    /** has-cavity-wall: yes. See WallInsulationHelper::getCavityWallValues(). */
    private const CAVITY_WALL_YES = 1;

    /**
     * The card for glass in order, by the glass of the living rooms: the measure that names what the
     * dwelling has. Triple glass is not put in poor frames, so HR+++ glass + frame holds for it too.
     * HR+ glass has a measure only for this card; SmartTwin has no product for it.
     */
    private const GLASS_CARDS = [
        GlassType::HR_PLUS->value      => 'hr-plus-glass-only',
        GlassType::HR_PLUS_PLUS->value => 'hrpp-glass-only',
        GlassType::TRIPLE->value       => 'hr3p-frames',
        GlassType::VACUUM->value       => 'vacuum-glass-only',
    ];

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

        if (! $advised(Element::WINDOWS) && ! is_null($glassCard = $this->glassCard($building))) {
            $cards[] = $glassCard;
        }

        return $cards;
    }

    private function glassCard(Building $building): ?string
    {
        $elementValue = ElementValue::find($this->answer($building, 'current-living-rooms-windows'));
        $glassType = $elementValue instanceof ElementValue ? GlassType::of($elementValue) : null;

        if (! $glassType instanceof GlassType || ! $glassType->isInOrder()) {
            return null;
        }

        return self::GLASS_CARDS[$glassType->value] ?? null;
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
