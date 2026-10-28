<?php

namespace App\Services\SmartTwin\Mapping\Elements;

use App\Services\SmartTwin\Mapping\Mappers\FacadeParts;
use App\Services\SmartTwin\Mapping\Mappers\FloorParts;
use App\Services\SmartTwin\Mapping\Mappers\InsulatedParts;
use App\Services\SmartTwin\Mapping\Mappers\RoofParts;

/**
 * Reads one element of the dwelling out of one section of a SmartTwin response.
 *
 * One reading per element, never per measure: what a dwelling has is the same whichever measure
 * looks at it. The fields kept are SmartTwin's own, under snake_case names, with its codes as they
 * are (see the Advice API spec for their meaning). Surfaces in m², Rc in m²K/W, U in W/m²K, peak
 * power in Wp.
 */
class ElementReader
{
    /**
     * @param  array<string, mixed>  $response
     * @param  string                $section  `current` or `scenario`.
     * @param  null|RoofShape        $shape    For the roof: only the flat or the pitched part.
     */
    public function read(Element $element, array $response, string $section, ?RoofShape $shape = null): ElementState
    {
        return match ($element) {
            Element::FACADE        => $this->insulated(FacadeParts::fromResponse($response, $section), 'facadeType'),
            Element::FLOOR         => $this->insulated(FloorParts::fromResponse($response, $section), 'floorType'),
            Element::ROOF          => $this->roof($response, $section, $shape),
            Element::CRAWLSPACE    => $this->crawlspace(PartList::inAssemblies($response, $section, ['floorAssemblies' => 'crawlspaces'])),
            Element::WINDOWS       => $this->windows(PartList::inAssemblies($response, $section, ['facadeAssemblies' => 'windows', 'roofAssemblies' => 'windows'])),
            Element::DOORS         => $this->doors(PartList::inAssemblies($response, $section, ['facadeAssemblies' => 'doors'])),
            Element::PANELS        => $this->panels(PartList::inAssemblies($response, $section, ['facadeAssemblies' => 'panels'])),
            Element::PV            => $this->pv(PartList::systems($response, $section, 'pvSystems')),
            Element::VENTILATION   => $this->first(PartList::systems($response, $section, 'ventilationSystems'), [
                'system_type' => 'systemType', 'heat_recovery' => 'hasHeatRecovery', 'description' => 'description',
            ]),
            Element::HEATING       => $this->heating(PartList::systems($response, $section, 'heatingSystems')),
            Element::HOT_WATER     => $this->first(PartList::systems($response, $section, 'hotWaterSystems'), [
                'generator_type' => 'generatorType', 'combi' => 'isCombiDevice', 'cw_class' => 'cwClass',
                'shower_heat_recovery' => 'showerHeatRecoveryPresent', 'storage_vessel' => 'storageVesselPresent',
                'individual' => 'individualInstallation', 'description' => 'description',
            ]),
            Element::COOLING       => $this->first(PartList::systems($response, $section, 'coolingSystems'), [
                'generator_type' => 'generatorType', 'energy_carrier' => 'energyCarrier', 'heat_pump_source' => 'heatPumpSource',
                'description' => 'description',
            ]),
            Element::SOLAR_THERMAL => $this->first(PartList::systems($response, $section, 'solarSystems'), [
                'system_type' => 'systemType', 'collector_type' => 'collectorType', 'collector_area' => 'collectorArea',
                'collectors' => 'numberOfCollectors', 'orientation' => 'orientation', 'inclination' => 'inclination',
                'pvt' => 'isPVT', 'description' => 'description',
            ]),
        };
    }

    /**
     * Facade and floor: the type with the most surface, the Rc weighted by surface, the total
     * surface, and a line per type and Rc. The same sums the answers are calculated with.
     */
    private function insulated(InsulatedParts $parts, string $typeField): ElementState
    {
        if (0 === $parts->count()) {
            return new ElementState(null, [], []);
        }

        $rcValue = $parts->weightedRcValue();

        return new ElementState(
            [
                'type'     => $parts->dominantType($typeField),
                'rc_value' => is_null($rcValue) ? null : round($rcValue, 2),
                'area'     => round($parts->totalArea(), 2),
            ],
            $parts->lines($typeField),
            $parts->ids(),
        );
    }

    /**
     * The roof by shape — flat or pitched — rather than by construction, which is what Hoomdossier
     * asks and the woonplan lists ("Hellend dak", "Plat dak").
     *
     * @param  array<string, mixed>  $response
     */
    private function roof(array $response, string $section, ?RoofShape $shape): ElementState
    {
        $parts = RoofParts::fromResponse($response, $section);
        $parts = is_null($shape) ? $parts : $parts->ofShape($shape);

        if (0 === $parts->count()) {
            return new ElementState(null, [], []);
        }

        $rcValue = $parts->weightedRcValue();

        return new ElementState(
            [
                'type'     => $parts->dominantShape()?->value,
                'rc_value' => is_null($rcValue) ? null : round($rcValue, 2),
                'area'     => round($parts->totalArea(), 2),
            ],
            $parts->lines('shape'),
            $parts->ids(),
        );
    }

    /**
     * Whether there is a crawlspace counts the way has-crawlspace does: any height other than 0.
     */
    private function crawlspace(PartList $crawlspaces): ElementState
    {
        $lines = $crawlspaces->each([
            'height_above_ground_level' => 'heightAboveGroundLevel',
            'floor_insulation'          => 'floorInsulation',
            'ventilation'               => 'ventilation',
        ]);

        $present = array_values(array_filter(
            $lines,
            fn (array $line) => ! is_null($line['height_above_ground_level']) && 0.0 !== abs((float) $line['height_above_ground_level']),
        ));

        $first = $present[0] ?? $lines[0] ?? null;

        return new ElementState(
            is_null($first) ? ['present' => false] : array_merge(['present' => ! empty($present)], $first),
            $lines,
            $crawlspaces->ids(),
        );
    }

    private function windows(PartList $windows): ElementState
    {
        if (0 === $windows->count()) {
            return new ElementState(null, [], []);
        }

        return new ElementState(
            [
                'glass_type'     => $windows->dominant('glassType'),
                'frame_material' => $windows->dominant('frameMaterial'),
                'u_value'        => $windows->weighted('uValue'),
                'area'           => $windows->sum('area'),
                'count'          => $windows->count(),
            ],
            $windows->lines(['glass_type' => 'glassType', 'frame_material' => 'frameMaterial'], ['u_value' => 'uValue']),
            $windows->ids(),
        );
    }

    private function doors(PartList $doors): ElementState
    {
        if (0 === $doors->count()) {
            return new ElementState(null, [], []);
        }

        return new ElementState(
            [
                'insulated' => $doors->dominant('insulationInConstruction'),
                'u_value'   => $doors->weighted('uValue'),
                'area'      => $doors->sum('area'),
                'count'     => $doors->count(),
            ],
            $doors->lines(['insulated' => 'insulationInConstruction'], ['u_value' => 'uValue']),
            $doors->ids(),
        );
    }

    /** Note: SmartTwin's insulationPresent runs 0 = yes, 1 = no, 2 = unknown. Kept as it is. */
    private function panels(PartList $panels): ElementState
    {
        if (0 === $panels->count()) {
            return new ElementState(null, [], []);
        }

        return new ElementState(
            [
                'insulation_present' => $panels->dominant('insulationPresent'),
                'frame_type'         => $panels->dominant('frameType'),
                'u_value'            => $panels->weighted('uValue'),
                'area'               => $panels->sum('area'),
                'count'              => $panels->count(),
            ],
            $panels->lines(['insulation_present' => 'insulationPresent', 'frame_type' => 'frameType'], ['u_value' => 'uValue']),
            $panels->ids(),
        );
    }

    /** Peak power is the surface times the peak power per m², which is what SmartTwin gives. */
    private function pv(PartList $systems): ElementState
    {
        if (0 === $systems->count()) {
            return new ElementState(null, [], []);
        }

        $wattPeak = fn (array $system) => round((float) ($system['surfaceArea'] ?? 0) * (float) ($system['wattPeakPerM2'] ?? 0));

        $lines = $systems->each([
            'panel_count' => 'numberOfPanels',
            'watt_peak'   => $wattPeak,
            'orientation' => 'orientation',
            'inclination' => 'inclination',
            'panel_type'  => 'panelType',
            'area'        => 'surfaceArea',
        ]);

        return new ElementState(
            [
                'panel_count' => (int) array_sum(array_column($lines, 'panel_count')),
                'watt_peak'   => array_sum(array_column($lines, 'watt_peak')),
                'orientation' => $systems->dominant('orientation', 'numberOfPanels'),
                'area'        => $systems->sum('surfaceArea'),
            ],
            $lines,
            $systems->ids(),
        );
    }

    private function heating(PartList $systems): ElementState
    {
        $lines = $systems->each([
            'emitter_type' => 'emitterType',
            'individual'   => 'individualInstallation',
            'description'  => 'description',
            'generators'   => fn (array $system) => (new PartList($system['generators'] ?? []))->each([
                'generator_type'   => 'generatorType',
                'energy_carrier'   => 'energyCarrier',
                'heat_pump_source' => 'heatPumpSource',
                'description'      => 'description',
            ]),
        ]);

        $generatorIds = [];
        foreach ($systems->parts as $system) {
            array_push($generatorIds, ...(new PartList($system['generators'] ?? []))->ids());
        }

        return new ElementState($lines[0] ?? null, $lines, array_merge($systems->ids(), $generatorIds));
    }

    /**
     * Installations Hoomdossier asks about as one: the first system is the summary, every system a
     * line.
     *
     * @param  array<string, string>  $fields
     */
    private function first(PartList $systems, array $fields): ElementState
    {
        $lines = $systems->each($fields);

        return new ElementState($lines[0] ?? null, $lines, $systems->ids());
    }
}
