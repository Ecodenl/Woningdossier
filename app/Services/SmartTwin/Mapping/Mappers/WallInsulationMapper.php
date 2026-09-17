<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The facade of the wall insulation step.
 *
 * Fills three of its questions: the facade surface, the surface the advice insulates, and whether
 * the dwelling has a cavity wall. The fourth, current-wall-insulation, waits for the "Redelijk"
 * element value to exist.
 *
 * Every answer is one number derived from many facade parts, so of the leaves in a path group
 * exactly one carries the answer — the first part in the response — and the rest are skipped
 * pointing at it. Which leaf that is comes from the response itself, not from a counter, so it
 * holds however the assemblies are ordered and whichever of them turn out to be empty.
 */
class WallInsulationMapper implements FieldMapper
{
    private const CURRENT = 'current';
    private const SCENARIO = 'scenario';

    private const PART = '.properties.facadeAssemblies.*.facades';

    private const PATH_CURRENT_PARTS = self::CURRENT . self::PART;
    private const PATH_CURRENT_AREA = self::CURRENT . self::PART . '.*.area';
    private const PATH_CURRENT_TYPE = self::CURRENT . self::PART . '.*.facadeType';
    private const PATH_CURRENT_RC = self::CURRENT . self::PART . '.*.rcValue';
    private const PATH_CURRENT_CAVITY = self::CURRENT . self::PART . '.*.cavityThickness';
    private const PATH_SCENARIO_AREA = self::SCENARIO . self::PART . '.*.area';
    private const PATH_SCENARIO_RC = self::SCENARIO . self::PART . '.*.rcValue';

    /** Answers of has-cavity-wall, see WallInsulationHelper::getCavityWallValues(). */
    private const CAVITY_WALL_YES = 1;
    private const CAVITY_WALL_NO = 2;

    /** facadeType, per the Advice API spec. */
    private const TYPE_SINGLE_WALL = 0;
    private const TYPE_CAVITY_WALL = 1;
    private const TYPE_HSB_FACADE = 2;

    public function paths(): array
    {
        return [
            // An assembly without closed parts — a wall that is all window — is a leaf of its own:
            // the list is empty, so none of the paths below exist for it. Claimed so the report says
            // it was understood rather than never seen.
            self::PATH_CURRENT_PARTS,
            self::PATH_CURRENT_AREA,
            self::PATH_CURRENT_TYPE,
            self::PATH_CURRENT_RC,
            self::PATH_CURRENT_CAVITY,
            self::PATH_SCENARIO_AREA,
            self::PATH_SCENARIO_RC,
        ];
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        return match ($leaf->pathGroup) {
            self::PATH_CURRENT_AREA   => $this->wallSurface($leaf, $response),
            self::PATH_CURRENT_TYPE   => $this->hasCavityWall($leaf, $response),
            self::PATH_SCENARIO_AREA  => $this->insulationWallSurface($leaf, $response),
            self::PATH_CURRENT_PARTS  => MappingResult::skipped('assembly zonder dichte geveldelen'),
            self::PATH_CURRENT_RC     => MappingResult::skipped('bepaalt straks current-wall-insulation'),
            self::PATH_SCENARIO_RC    => MappingResult::skipped('bepaalt welk geveldeel geïsoleerd wordt, zie insulation-wall-surface'),
            // The spec calls this "only relevant for cavity walls", and it drops to 0 in the
            // scenario once the cavity is filled. facadeType says the same thing and keeps saying it.
            self::PATH_CURRENT_CAVITY => MappingResult::skipped('facadeType is het signaal voor has-cavity-wall'),
            // Only reachable if paths() and this match drift apart, which would be a bug here
            // rather than a gap in the mapping.
            default                   => MappingResult::skipped('path group wordt geclaimd maar niet afgehandeld'),
        };
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function wallSurface(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirstPart($leaf, $response, self::CURRENT, 'area')) {
            return MappingResult::skipped('telt mee in wall-surface op het eerste geveldeel');
        }

        $parts = FacadeParts::fromResponse($response, self::CURRENT);
        $area = $parts->totalArea();

        if ($area <= 0) {
            return MappingResult::valueUnmapped('geveldelen zonder oppervlak');
        }

        return MappingResult::mapped(
            'wall-surface',
            round($area, 2),
            "som van {$parts->count()} dichte geveldelen, zonder ramen, deuren en panelen",
        );
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function insulationWallSurface(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirstPart($leaf, $response, self::SCENARIO, 'area')) {
            return MappingResult::skipped('telt mee in insulation-wall-surface op het eerste geveldeel');
        }

        $current = FacadeParts::fromResponse($response, self::CURRENT);
        $area = FacadeParts::fromResponse($response, self::SCENARIO)->areaImprovedOver($current);

        if (is_null($area)) {
            return MappingResult::valueUnmapped('current en scenario beschrijven een verschillend aantal geveldelen');
        }

        if ($area <= 0) {
            // Not the same as zero: SmartTwin advises no facade measure, so it says nothing about
            // how much facade would be insulated. Writing 0 would put words in its mouth, and that
            // number drives both the cost and the savings of the measure.
            return MappingResult::skipped('het advies isoleert geen gevel');
        }

        return MappingResult::mapped(
            'insulation-wall-surface',
            round($area, 2),
            'geveldelen waarvan de Rc-waarde stijgt tussen current en scenario',
        );
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function hasCavityWall(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirstPart($leaf, $response, self::CURRENT, 'facadeType')) {
            return MappingResult::skipped('telt mee in has-cavity-wall op het eerste geveldeel');
        }

        $type = FacadeParts::fromResponse($response, self::CURRENT)->dominantType();

        if (is_null($type)) {
            return MappingResult::valueUnmapped('geen geveldeel geeft een facadeType');
        }

        return match ($type) {
            self::TYPE_CAVITY_WALL => MappingResult::mapped('has-cavity-wall', self::CAVITY_WALL_YES, 'spouwmuur beslaat het grootste geveloppervlak'),
            self::TYPE_SINGLE_WALL => MappingResult::mapped('has-cavity-wall', self::CAVITY_WALL_NO, 'massieve gevel beslaat het grootste geveloppervlak'),
            self::TYPE_HSB_FACADE  => MappingResult::mapped('has-cavity-wall', self::CAVITY_WALL_NO, 'houtskeletbouw beslaat het grootste geveloppervlak'),
            default                => MappingResult::valueUnmapped("onbekend facadeType {$type}"),
        };
    }

    /**
     * Whether another leaf of this path group carries the answer.
     *
     * The first part in the response is the one that does. Index 0 of assembly 0 is no good: an
     * assembly that is all window has no parts at all, and in a real response that is the first one.
     *
     * @param  array<string, mixed>  $response
     */
    private function notTheFirstPart(Leaf $leaf, array $response, string $section, string $field): bool
    {
        foreach ($response[$section]['properties']['facadeAssemblies'] ?? [] as $assembly => $parts) {
            foreach (array_keys($parts['facades'] ?? []) as $part) {
                return $leaf->path !== "{$section}.properties.facadeAssemblies.{$assembly}.facades.{$part}.{$field}";
            }
        }

        return true;
    }
}
