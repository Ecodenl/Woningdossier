<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\Elements\AdvisedElements;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The facade of the wall insulation step.
 *
 * Fills four of its questions: the facade surface, the surface the scenario insulates, whether the
 * dwelling has a cavity wall, and how well the facade is insulated. The remaining five are about
 * the state of the brickwork and the paintwork, which SmartTwin does not describe.
 *
 * Every answer is one number derived from many facade parts, so all of them are decided at one leaf
 * — the anchor, see ElementAnchor — and every other leaf is skipped pointing at the answer it counts
 * towards. An answer the response no longer supports is emptied rather than left as the previous
 * scan wrote it.
 */
class WallInsulationMapper implements FieldMapper
{
    private const CURRENT = 'current';
    private const SCENARIO = 'scenario';

    private const ASSEMBLIES = '.properties.facadeAssemblies';
    private const PART = self::ASSEMBLIES . '.*.facades';

    /** Answers of has-cavity-wall, see WallInsulationHelper::getCavityWallValues(). */
    private const CAVITY_WALL_YES = 1;
    private const CAVITY_WALL_NO = 2;

    /** facadeType, per the Advice API spec. */
    private const TYPE_SINGLE_WALL = 0;
    private const TYPE_CAVITY_WALL = 1;
    private const TYPE_HSB_FACADE = 2;

    /** What each claimed field of a facade part counts towards, for the report. */
    private const NOTES = [
        'area'            => 'telt mee in wall-surface',
        'rcValue'         => 'telt mee in current-wall-insulation',
        'facadeType'      => 'telt mee in has-cavity-wall',
        'id'              => 'koppelt het geveldeel tussen current en scenario',
        // The spec calls this "only relevant for cavity walls", and it drops to 0 in the scenario
        // once the cavity is filled. facadeType says the same thing and keeps saying it.
        'cavityThickness' => 'facadeType is het signaal voor has-cavity-wall',
    ];

    public function __construct(
        private readonly ElementValues $elementValues,
        private readonly AdvisedElements $advisedElements,
    )
    {
    }

    public function paths(): array
    {
        return array_merge(
            [
                self::CURRENT . self::ASSEMBLIES,
                // An assembly without closed parts — a wall that is all window — is a leaf of its own:
                // the list is empty, so none of the paths below exist for it.
                self::CURRENT . self::PART,
                self::SCENARIO . self::PART . '.*.area',
                self::SCENARIO . self::PART . '.*.rcValue',
                self::SCENARIO . self::PART . '.*.id',
            ],
            array_map(fn (string $field) => self::CURRENT . self::PART . ".*.{$field}", array_keys(self::NOTES)),
        );
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult|array
    {
        if (ElementAnchor::is($leaf, $response, self::CURRENT . self::ASSEMBLIES, $this->paths())) {
            return $this->answers($response);
        }

        return MappingResult::skipped($this->noteFor($leaf));
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, MappingResult>
     */
    private function answers(array $response): array
    {
        $current = FacadeParts::fromResponse($response, self::CURRENT);
        $insulated = FacadeParts::fromResponse($response, self::SCENARIO)->improvedOver($current);

        $results = [
            $this->wallSurface($current),
            $this->hasCavityWall($current),
            $this->currentWallInsulation($current),
            $this->insulationWallSurface($insulated),
        ];

        $disagreement = $this->disagreement($response, $insulated);

        return is_null($disagreement) ? $results : [...$results, $disagreement];
    }

    private function wallSurface(FacadeParts $parts): MappingResult
    {
        $area = $parts->totalArea();

        if ($area <= 0) {
            return MappingResult::cleared('wall-surface', 'geen dicht geveldeel met oppervlak in de response');
        }

        return MappingResult::mapped(
            'wall-surface',
            round($area, 2),
            "som van {$parts->count()} dichte geveldelen, zonder ramen, deuren en panelen",
        );
    }

    private function insulationWallSurface(FacadeParts $insulated): MappingResult
    {
        if ($insulated->totalArea() <= 0) {
            // Empty, not 0: the scenario insulates no facade, which says nothing about how much
            // facade would be insulated if it did. 0 would also feed a cost and saving calculation.
            return MappingResult::cleared('insulation-wall-surface', 'het scenario verhoogt bij geen geveldeel de Rc-waarde');
        }

        return MappingResult::mapped(
            'insulation-wall-surface',
            round($insulated->totalArea(), 2),
            "{$insulated->count()} geveldeel/-delen waarvan de Rc-waarde stijgt tussen current en scenario",
        );
    }

    private function currentWallInsulation(FacadeParts $parts): MappingResult
    {
        $rcValue = $parts->weightedRcValue();

        if (is_null($rcValue)) {
            // Emptied rather than "Onbekend": that is an answer, and not one SmartTwin gave.
            return MappingResult::cleared('current-wall-insulation', 'geen Rc-waarde te bepalen zonder geveloppervlak');
        }

        $calculateValue = InsulationQuality::forWall($rcValue);
        $elementValueId = $this->elementValues->idFor('wall-insulation', $calculateValue);

        if (is_null($elementValueId)) {
            // Most likely upgrade:extend-insulation-scales has not run on this database, so
            // the scale is one level short. Reported as a broken mapping rather than a gap in it,
            // because that is what it is.
            return MappingResult::targetMissing(
                'current-wall-insulation',
                $calculateValue,
                "geen element value met calculate_value {$calculateValue} voor wall-insulation",
            );
        }

        // The label of the option that was written, not a calculate value: whoever reads the report
        // checks it against the classification table, which knows names and boundaries only.
        $label = $this->elementValues->labelFor($elementValueId) ?? "element value {$elementValueId}";

        return MappingResult::mapped(
            'current-wall-insulation',
            $elementValueId,
            'oppervlakte-gewogen ' . InsulationQuality::describeWall($rcValue) . " → {$label}",
        );
    }

    private function hasCavityWall(FacadeParts $parts): MappingResult
    {
        $type = $parts->dominantType('facadeType');

        if (is_null($type)) {
            return MappingResult::cleared('has-cavity-wall', 'geen geveldeel geeft een facadeType');
        }

        return match ($type) {
            self::TYPE_CAVITY_WALL => MappingResult::mapped('has-cavity-wall', self::CAVITY_WALL_YES, 'spouwmuur beslaat het grootste geveloppervlak'),
            self::TYPE_SINGLE_WALL => MappingResult::mapped('has-cavity-wall', self::CAVITY_WALL_NO, 'massieve gevel beslaat het grootste geveloppervlak'),
            self::TYPE_HSB_FACADE  => MappingResult::mapped('has-cavity-wall', self::CAVITY_WALL_NO, 'houtskeletbouw beslaat het grootste geveloppervlak'),
            default                => MappingResult::valueUnmapped("onbekend facadeType {$type}"),
        };
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function disagreement(array $response, FacadeParts $insulated): ?MappingResult
    {
        $advised = $this->advisedElements->covers($response, Element::FACADE);
        $area = round($insulated->totalArea(), 2);

        return match (true) {
            $advised && $area <= 0 => MappingResult::specViolation('er is een solution voor de gevel, maar het scenario verhoogt bij geen geveldeel de Rc-waarde'),
            ! $advised && $area > 0 => MappingResult::specViolation("het scenario verhoogt de Rc-waarde van {$area} m² gevel, maar er is geen solution voor de gevel"),
            default => null,
        };
    }

    private function noteFor(Leaf $leaf): string
    {
        return match (true) {
            str_starts_with($leaf->pathGroup, self::SCENARIO)     => 'bepaalt insulation-wall-surface',
            $leaf->pathGroup === self::CURRENT . self::ASSEMBLIES => 'woning zonder gevel: de gevelvragen worden leeggemaakt',
            $leaf->pathGroup === self::CURRENT . self::PART       => 'assembly zonder dichte geveldelen',
            default                                               => self::NOTES[$leaf->key()] ?? 'path group wordt geclaimd maar niet afgehandeld',
        };
    }
}
