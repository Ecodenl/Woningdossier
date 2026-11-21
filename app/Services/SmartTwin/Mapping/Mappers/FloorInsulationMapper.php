<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\Elements\AdvisedElements;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The ground floor of the floor insulation step.
 *
 * Fills five of its questions: the floor surface, the surface the scenario insulates, how well the
 * floor is insulated, and whether there is a crawlspace and how high it is. Every answer is one
 * number out of many parts, so all of them are decided at one leaf — the anchor, see ElementAnchor —
 * and every other leaf is skipped pointing at the answer it counts towards.
 *
 * An import takes over what SmartTwin describes. An answer the response no longer supports is
 * emptied rather than left as the previous scan wrote it.
 */
class FloorInsulationMapper implements FieldMapper
{
    private const CURRENT = 'current';
    private const SCENARIO = 'scenario';

    private const ASSEMBLIES = '.properties.floorAssemblies';
    private const FLOOR = self::ASSEMBLIES . '.*.floors';
    private const CRAWLSPACE = self::ASSEMBLIES . '.*.crawlspaces';

    /** Answers of has-crawlspace, see App\Helpers\QuestionValues\HasCrawlspace. */
    private const HAS_CRAWLSPACE_YES = 'yes';
    private const HAS_CRAWLSPACE_NO = 'no';
    private const HAS_CRAWLSPACE_UNKNOWN = 'unknown';

    /** What each claimed field of a floor or crawlspace counts towards, for the report. */
    private const FLOOR_NOTES = [
        'area'    => 'telt mee in floor-surface',
        'rcValue' => 'telt mee in current-floor-insulation',
        'id'      => 'koppelt het vloerdeel tussen current en scenario',
    ];

    private const FLOOR_FIELDS = ['cavity', 'description', 'floorType', 'insulationExterior',
                                  'insulationInConstruction', 'insulationInterior', 'insulationThickness',
                                  'thermoPillows'];

    private const CRAWLSPACE_FIELDS = ['area', 'description', 'floorInsulation', 'floorRbf', 'floorRbw',
                                       'ventilation', 'id'];

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
                self::CURRENT . self::FLOOR,
                self::CURRENT . self::CRAWLSPACE,
                self::CURRENT . self::CRAWLSPACE . '.*.heightAboveGroundLevel',
                self::SCENARIO . self::FLOOR . '.*.area',
                self::SCENARIO . self::FLOOR . '.*.rcValue',
                self::SCENARIO . self::FLOOR . '.*.id',
            ],
            array_map(fn (string $f) => self::CURRENT . self::FLOOR . ".*.{$f}", array_keys(self::FLOOR_NOTES)),
            array_map(fn (string $f) => self::CURRENT . self::FLOOR . ".*.{$f}", self::FLOOR_FIELDS),
            array_map(fn (string $f) => self::CURRENT . self::CRAWLSPACE . ".*.{$f}", self::CRAWLSPACE_FIELDS),
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
        $current = FloorParts::fromResponse($response, self::CURRENT);
        $insulated = FloorParts::fromResponse($response, self::SCENARIO)->improvedOver($current);
        $crawlspaces = $this->crawlspaces($response);

        return array_values(array_filter([
            $this->floorSurface($current),
            $this->currentFloorInsulation($current),
            $this->insulationFloorSurface($insulated),
            ...$this->crawlspace($crawlspaces),
            $this->disagreement($response, $insulated),
        ]));
    }

    private function floorSurface(FloorParts $parts): MappingResult
    {
        $area = $parts->totalArea();

        if ($area <= 0) {
            return MappingResult::cleared('floor-surface', 'geen vloer met oppervlak in de response');
        }

        return MappingResult::mapped('floor-surface', round($area, 2), "som van {$parts->count()} vloer(en)");
    }

    private function insulationFloorSurface(FloorParts $insulated): MappingResult
    {
        if ($insulated->totalArea() <= 0) {
            // Empty, not 0: the scenario insulates no floor, which says nothing about how much floor
            // would be insulated if it did. 0 would also feed a cost and saving calculation.
            return MappingResult::cleared('insulation-floor-surface', 'het scenario verhoogt bij geen vloerdeel de Rc-waarde');
        }

        return MappingResult::mapped(
            'insulation-floor-surface',
            round($insulated->totalArea(), 2),
            "{$insulated->count()} vloerdeel/-delen waarvan de Rc-waarde stijgt tussen current en scenario",
        );
    }

    private function currentFloorInsulation(FloorParts $parts): MappingResult
    {
        $rcValue = $parts->weightedRcValue();

        if (is_null($rcValue)) {
            return MappingResult::cleared('current-floor-insulation', 'geen Rc-waarde te bepalen zonder vloeroppervlak');
        }

        $calculateValue = InsulationQuality::forFloor($rcValue);
        $elementValueId = $this->elementValues->idFor('floor-insulation', $calculateValue);

        if (is_null($elementValueId)) {
            // Most likely upgrade:extend-insulation-scales has not run on this database, so the
            // scale is short of a level. A deploy that went wrong, not a gap in the mapping.
            return MappingResult::targetMissing(
                'current-floor-insulation',
                $calculateValue,
                "geen element value met calculate_value {$calculateValue} voor floor-insulation",
            );
        }

        $label = $this->elementValues->labelFor($elementValueId) ?? "element value {$elementValueId}";

        return MappingResult::mapped(
            'current-floor-insulation',
            $elementValueId,
            'oppervlakte-gewogen ' . InsulationQuality::describeFloor($rcValue) . " → {$label}",
        );
    }

    /**
     * Whether there is a crawlspace, and how high.
     *
     * `heightAboveGroundLevel` is the height of the crawlspace floor relative to ground level, and
     * only its size matters (stakeholder decision): any height other than 0 is a crawlspace. No
     * crawlspace in the response, or one at height 0, is a "nee" — SmartTwin describes the dwelling,
     * and a crawlspace it leaves out is one the dwelling does not have. A crawlspace without a height
     * leaves it open.
     *
     * More than one crawlspace is something neither question covers, and nothing in the response
     * ranks them; their area is 0 in every dossier seen so far. The first one with a height answers.
     *
     * @param  array<int, array<string, mixed>>  $crawlspaces
     * @return array<int, MappingResult>
     */
    private function crawlspace(array $crawlspaces): array
    {
        $heights = array_map(
            fn (array $crawlspace) => isset($crawlspace['heightAboveGroundLevel']) ? (float) $crawlspace['heightAboveGroundLevel'] : null,
            $crawlspaces,
        );

        $present = array_values(array_filter($heights, fn (?float $height) => ! is_null($height) && 0.0 !== abs($height)));

        if (! empty($present)) {
            return [
                MappingResult::mapped('has-crawlspace', self::HAS_CRAWLSPACE_YES, 'de response beschrijft een kruipruimte met een hoogte t.o.v. maaiveld'),
                $this->crawlspaceHeight($present[0]),
            ];
        }

        if (in_array(null, $heights, true)) {
            return [
                MappingResult::mapped('has-crawlspace', self::HAS_CRAWLSPACE_UNKNOWN, 'de response beschrijft een kruipruimte zonder hoogte'),
                $this->crawlspaceHeight(null),
            ];
        }

        $why = empty($crawlspaces) ? 'de response beschrijft geen kruipruimte' : 'de kruipruimte ligt op maaiveldhoogte (0 m)';

        return [
            MappingResult::mapped('has-crawlspace', self::HAS_CRAWLSPACE_NO, $why),
            MappingResult::cleared('crawlspace-height', $why),
        ];
    }

    private function crawlspaceHeight(?float $height): MappingResult
    {
        $order = CrawlspaceHeight::orderFor($height);
        $elementValueId = $this->elementValues->idForOrder('crawlspace', $order);

        if (is_null($elementValueId)) {
            return MappingResult::targetMissing(
                'crawlspace-height',
                $order,
                "geen element value met order {$order} voor crawlspace",
            );
        }

        $label = $this->elementValues->labelFor($elementValueId) ?? "element value {$elementValueId}";

        return MappingResult::mapped(
            'crawlspace-height',
            $elementValueId,
            CrawlspaceHeight::describe($height) . " → {$label}",
        );
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function disagreement(array $response, FloorParts $insulated): ?MappingResult
    {
        $advised = $this->advisedElements->covers($response, Element::FLOOR);
        $area = round($insulated->totalArea(), 2);

        return match (true) {
            $advised && $area <= 0 => MappingResult::specViolation('er is een solution voor de vloer, maar het scenario verhoogt bij geen vloerdeel de Rc-waarde'),
            ! $advised && $area > 0 => MappingResult::specViolation("het scenario verhoogt de Rc-waarde van {$area} m² vloer, maar er is geen solution voor de vloer"),
            default => null,
        };
    }

    /**
     * Every crawlspace of the current situation, out of the floor assemblies that hold them.
     *
     * @param  array<string, mixed>  $response
     * @return array<int, array<string, mixed>>
     */
    private function crawlspaces(array $response): array
    {
        $crawlspaces = [];

        foreach ($response[self::CURRENT]['properties']['floorAssemblies'] ?? [] as $assembly) {
            foreach ($assembly['crawlspaces'] ?? [] as $crawlspace) {
                $crawlspaces[] = $crawlspace;
            }
        }

        return $crawlspaces;
    }

    private function noteFor(Leaf $leaf): string
    {
        $field = $leaf->key();

        return match (true) {
            str_starts_with($leaf->pathGroup, self::SCENARIO)                          => 'bepaalt insulation-floor-surface',
            $leaf->pathGroup === self::CURRENT . self::ASSEMBLIES                      => 'woning zonder vloer: de vloervragen worden leeggemaakt',
            $leaf->pathGroup === self::CURRENT . self::FLOOR                           => 'assembly zonder vloeren',
            $leaf->pathGroup === self::CURRENT . self::CRAWLSPACE                      => 'assembly zonder kruipruimte',
            str_contains($leaf->pathGroup, '.crawlspaces.') && 'heightAboveGroundLevel' === $field => 'bepaalt has-crawlspace en crawlspace-height',
            str_contains($leaf->pathGroup, '.crawlspaces.')                            => 'geen vraag in Hoomdossier voor dit kenmerk van de kruipruimte',
            isset(self::FLOOR_NOTES[$field])                                           => self::FLOOR_NOTES[$field],
            default                                                                    => 'geen vraag in Hoomdossier voor dit vloerkenmerk',
        };
    }
}
