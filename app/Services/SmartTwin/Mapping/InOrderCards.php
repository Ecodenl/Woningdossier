<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\GlassType;
use App\Models\Building;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Services\SmartTwin\InstallationOptions;
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
 * in order from HR (GlassType), and the card is Hoomdossier's own measure for the glass the dwelling
 * has, or none.
 *
 * Installations since HD-21 (November 2026), replacing decision I4 of 7 November ("no In orde cards
 * for installations"). The client listed what is in order: a heat pump (hybrid or full, any source),
 * district heating, biomass, a solar boiler, a shower heat recovery unit, mechanical ventilation that
 * is demand driven, balanced or decentral ventilation with heat recovery, solar panels, and electric
 * cooking. As SmartTwin advises what is not in order, an installation it advises nothing for is. Each
 * gets the measure naming what the dwelling has; see the constants for where that is approximate.
 */
class InOrderCards
{
    /** has-cavity-wall: yes. See WallInsulationHelper::getCavityWallValues(). */
    private const CAVITY_WALL_YES = 1;

    /**
     * The card for glass in order, by the glass of the living rooms: the measure that names what the
     * dwelling has. Triple glass is not put in poor frames, so HR+++ glass + frame holds for it too.
     * HR and HR+ glass have a measure only for this card; SmartTwin has no product for them.
     */
    private const GLASS_CARDS = [
        GlassType::HR->value           => 'hr-glass-only',
        GlassType::HR_PLUS->value      => 'hr-plus-glass-only',
        GlassType::HR_PLUS_PLUS->value => 'hrpp-glass-only',
        GlassType::TRIPLE->value       => 'hr3p-frames',
        GlassType::VACUUM->value       => 'vacuum-glass-only',
    ];

    /**
     * The card for a heat pump, by the order of its heat-pump-type service value. The six original
     * types have a measure of their own. The ones only SmartTwin gives (InstallationOptions) have none,
     * and get the nearest one in heatPumpCard(): a hybrid one that of outside air, a full one on a
     * ground or water source that of ground heat, any other full one that of outside air.
     */
    private const HEAT_PUMP_CARDS = [
        1 => 'hybrid-heat-pump-outside-air',
        2 => 'hybrid-heat-pump-ventilation-air',
        3 => 'hybrid-heat-pump-pvt-panels',
        4 => 'full-heat-pump-outside-air',
        5 => 'full-heat-pump-ground-heat',
        6 => 'full-heat-pump-pvt-panels',
    ];

    /**
     * Measures that replace the heat generator. Any of them advised means the heating is not in order;
     * advising radiators or floor heating leaves the generator as it is. The heat pumps are matched by
     * their prefix. Not ElementScope: every measure of the heating step covers the heating there,
     * including the emitters and the shower heat recovery.
     */
    private const HEAT_GENERATOR_MEASURES = [
        'high-efficiency-boiler-replace',
        'collective-combi-heat-pump-outside-air',
        'district-heating-connect',
        'biomass-boiler',
    ];

    /** ventilation-type, by the order of its house-ventilation service value (VentilationMapper). */
    private const VENTILATION_MECHANICAL = 2;
    private const VENTILATION_BALANCED = 3;
    private const VENTILATION_DECENTRAL = 4;

    /**
     * cook-type answers that are electric cooking. Its only card is the induction hob, so plain electric
     * cooking gets that one too. SmartTwin says nothing about cooking and the scans in a SmartTwin
     * cooperation do not ask it, so it is only there when the resident answered it before.
     */
    private const ELECTRIC_COOKING = ['electric', 'induction'];

    public function __construct(private readonly SolutionMeasures $measures)
    {
    }

    /**
     * @param  array<int, string>  $advisedMeasureShorts  The measures the package advises.
     * @param  array<string, mixed>  $results  The response, for what the dossier has no question for.
     * @return array<int, string> The measure shorts to put in "In orde".
     */
    public function for(Building $building, array $advisedMeasureShorts, array $results = []): array
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

        array_push($cards, ...$this->heatingCards($building, $advisedMeasureShorts));

        $hotWaterSources = (array) $this->answer($building, 'heat-source-warm-tap-water');
        if (! $advised(Element::SOLAR_THERMAL)
            && in_array('sun-boiler', [...(array) $this->answer($building, 'heat-source'), ...$hotWaterSources], true)
        ) {
            $cards[] = 'heater-place-replace';
        }

        if ($this->hasShowerHeatRecovery($results)) {
            $cards[] = 'shower-heat-recovery';
        }

        if (! $advised(Element::VENTILATION) && ! is_null($ventilationCard = $this->ventilationCard($building))) {
            $cards[] = $ventilationCard;
        }

        if (! $advised(Element::PV) && 'yes' === $this->answer($building, 'has-solar-panels')) {
            $cards[] = 'solar-panels-place-replace';
        }

        if (in_array($this->answer($building, 'cook-type'), self::ELECTRIC_COOKING, true)) {
            $cards[] = 'induction-hob';
        }

        // A measure SmartTwin advises is in its package, never in order: one card per measure.
        return array_values(array_unique(array_diff($cards, $advisedMeasureShorts)));
    }

    /**
     * @param  array<int, string>  $advisedMeasureShorts
     * @return array<int, string>
     */
    private function heatingCards(Building $building, array $advisedMeasureShorts): array
    {
        foreach ($advisedMeasureShorts as $measureShort) {
            if (in_array($measureShort, self::HEAT_GENERATOR_MEASURES, true)
                || str_starts_with($measureShort, 'hybrid-heat-pump-')
                || str_starts_with($measureShort, 'full-heat-pump-')
            ) {
                return [];
            }
        }

        $sources = (array) $this->answer($building, 'heat-source');
        $cards = [];

        if (in_array('heat-pump', $sources, true) && ! is_null($heatPumpCard = $this->heatPumpCard($building))) {
            $cards[] = $heatPumpCard;
        }

        if (in_array('district-heating', $sources, true)) {
            $cards[] = 'district-heating-connect';
        }

        if (in_array('biomass', $sources, true)) {
            $cards[] = 'biomass-boiler';
        }

        return $cards;
    }

    private function heatPumpCard(Building $building): ?string
    {
        $order = ServiceValue::find($this->answer($building, 'heat-pump-type'))?->order;
        $type = InstallationOptions::HEAT_PUMP_TYPES[$order] ?? null;

        return match (true) {
            is_null($order)                    => null,
            isset(self::HEAT_PUMP_CARDS[$order]) => self::HEAT_PUMP_CARDS[$order],
            is_null($type)                     => null,
            $type['hybrid']                    => 'hybrid-heat-pump-outside-air',
            in_array($type['source'], [InstallationOptions::SOURCE_GROUND, InstallationOptions::SOURCE_GROUNDWATER, InstallationOptions::SOURCE_SURFACE_WATER], true)
                                               => 'full-heat-pump-ground-heat',
            default                            => 'full-heat-pump-outside-air',
        };
    }

    private function ventilationCard(Building $building): ?string
    {
        $order = ServiceValue::find($this->answer($building, 'ventilation-type'))?->order;

        return match ($order) {
            self::VENTILATION_MECHANICAL => $this->isYes($building, 'ventilation-demand-driven') ? 'ventilation-demand-driven' : null,
            self::VENTILATION_BALANCED   => $this->isYes($building, 'ventilation-heat-recovery') ? 'ventilation-balanced-wtw' : null,
            self::VENTILATION_DECENTRAL  => $this->isYes($building, 'ventilation-heat-recovery') ? 'ventilation-decentral-wtw' : null,
            default                      => null,
        };
    }

    /**
     * Hoomdossier has no question for a shower heat recovery unit, so it is read from the response:
     * any hot water system that has one.
     *
     * @param  array<string, mixed>  $results
     */
    private function hasShowerHeatRecovery(array $results): bool
    {
        foreach ($results['current']['properties']['hotWaterSystems'] ?? [] as $system) {
            if (true === ($system['showerHeatRecoveryPresent'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    private function isYes(Building $building, string $toolQuestionShort): bool
    {
        return (bool) filter_var($this->answer($building, $toolQuestionShort), FILTER_VALIDATE_BOOLEAN);
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
