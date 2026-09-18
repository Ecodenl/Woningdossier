<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The ground floor of the floor insulation step.
 *
 * Fills five of its questions: the floor surface, the surface the advice insulates, how well the
 * floor is insulated, and whether there is a crawlspace and how high it is. Built the same way as the facade — each
 * answer is one number out of many parts, so one leaf of a path group carries it and the rest are
 * skipped pointing at it.
 */
class FloorInsulationMapper implements FieldMapper
{
    private const CURRENT = 'current';
    private const SCENARIO = 'scenario';

    private const FLOOR = '.properties.floorAssemblies.*.floors';
    private const CRAWLSPACE = '.properties.floorAssemblies.*.crawlspaces';

    private const PATH_CURRENT_FLOORS = self::CURRENT . self::FLOOR;
    private const PATH_CURRENT_AREA = self::CURRENT . self::FLOOR . '.*.area';
    private const PATH_CURRENT_RC = self::CURRENT . self::FLOOR . '.*.rcValue';
    private const PATH_SCENARIO_AREA = self::SCENARIO . self::FLOOR . '.*.area';
    private const PATH_SCENARIO_RC = self::SCENARIO . self::FLOOR . '.*.rcValue';

    private const PATH_CURRENT_CRAWLSPACES = self::CURRENT . self::CRAWLSPACE;

    /**
     * One field of a crawlspace answers has-crawlspace, and it has to be a named one. Anchoring on
     * "whichever field this leaf happens to be" writes the answer once per field.
     */
    private const PATH_CURRENT_CRAWLSPACE_AREA = self::CURRENT . self::CRAWLSPACE . '.*.area';

    private const PATH_CURRENT_CRAWLSPACE_HEIGHT = self::CURRENT . self::CRAWLSPACE . '.*.heightAboveGroundLevel';

    /** Answers of has-crawlspace, see App\Helpers\QuestionValues\HasCrawlspace. */
    private const HAS_CRAWLSPACE_YES = 'yes';

    public function __construct(private readonly ElementValues $elementValues)
    {
    }

    public function paths(): array
    {
        $floorFields = ['cavity', 'description', 'floorType', 'insulationExterior', 'insulationInConstruction',
                        'insulationInterior', 'insulationThickness', 'thermoPillows'];
        $crawlspaceFields = ['area', 'description', 'floorInsulation', 'floorRbf', 'floorRbw', 'ventilation'];

        return array_merge(
            [
                self::PATH_CURRENT_FLOORS,
                self::PATH_CURRENT_AREA,
                self::PATH_CURRENT_RC,
                self::PATH_SCENARIO_AREA,
                self::PATH_SCENARIO_RC,
                self::PATH_CURRENT_CRAWLSPACES,
                self::PATH_CURRENT_CRAWLSPACE_HEIGHT,
            ],
            array_map(fn (string $f) => self::CURRENT . self::FLOOR . ".*.{$f}", $floorFields),
            array_map(fn (string $f) => self::CURRENT . self::CRAWLSPACE . ".*.{$f}", $crawlspaceFields),
        );
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        return match ($leaf->pathGroup) {
            self::PATH_CURRENT_AREA        => $this->floorSurface($leaf, $response),
            self::PATH_CURRENT_RC          => $this->currentFloorInsulation($leaf, $response),
            self::PATH_SCENARIO_AREA       => $this->insulationFloorSurface($leaf, $response),
            self::PATH_CURRENT_CRAWLSPACE_AREA   => $this->hasCrawlspace($leaf, $response),
            self::PATH_CURRENT_CRAWLSPACE_HEIGHT => $this->crawlspaceHeight($leaf, $response),
            self::PATH_CURRENT_CRAWLSPACES     => MappingResult::skipped('assembly zonder kruipruimte'),
            self::PATH_CURRENT_FLOORS      => MappingResult::skipped('assembly zonder vloeren'),
            self::PATH_SCENARIO_RC         => MappingResult::skipped('bepaalt welke vloer geïsoleerd wordt, zie insulation-floor-surface'),

            default => $this->otherField($leaf),
        };
    }

    private function otherField(Leaf $leaf): MappingResult
    {
        return str_contains($leaf->pathGroup, '.crawlspaces.')
            ? MappingResult::skipped('has-crawlspace hangt aan het oppervlak van de kruipruimte')
            : MappingResult::skipped('geen vraag in Hoomdossier voor dit vloerkenmerk');
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function floorSurface(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirst($leaf, $response, self::CURRENT, 'floors', 'area')) {
            return MappingResult::skipped('telt mee in floor-surface op de eerste vloer');
        }

        $parts = FloorParts::fromResponse($response, self::CURRENT);
        $area = $parts->totalArea();

        if ($area <= 0) {
            return MappingResult::valueUnmapped('vloeren zonder oppervlak');
        }

        return MappingResult::mapped('floor-surface', round($area, 2), "som van {$parts->count()} vloer(en)");
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function insulationFloorSurface(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirst($leaf, $response, self::SCENARIO, 'floors', 'area')) {
            return MappingResult::skipped('telt mee in insulation-floor-surface op de eerste vloer');
        }

        $current = FloorParts::fromResponse($response, self::CURRENT);
        $area = FloorParts::fromResponse($response, self::SCENARIO)->areaImprovedOver($current);

        if (is_null($area)) {
            return MappingResult::valueUnmapped('current en scenario beschrijven een verschillend aantal vloeren');
        }

        if ($area <= 0) {
            // Same as the facade: SmartTwin advises no floor measure, so it says nothing about how
            // much floor would be insulated. Writing 0 would put words in its mouth, and that number
            // drives both the cost and the savings of the measure.
            return MappingResult::skipped('het advies isoleert geen vloer');
        }

        return MappingResult::mapped(
            'insulation-floor-surface',
            round($area, 2),
            'vloeren waarvan de Rc-waarde stijgt tussen current en scenario',
        );
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function currentFloorInsulation(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirst($leaf, $response, self::CURRENT, 'floors', 'rcValue')) {
            return MappingResult::skipped('telt mee in current-floor-insulation op de eerste vloer');
        }

        $rcValue = FloorParts::fromResponse($response, self::CURRENT)->weightedRcValue();

        if (is_null($rcValue)) {
            return MappingResult::valueUnmapped('geen Rc-waarde te bepalen zonder vloeroppervlak');
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

        return MappingResult::mapped(
            'current-floor-insulation',
            $elementValueId,
            'oppervlakte-gewogen Rc ' . round($rcValue, 2) . " valt in klasse {$calculateValue}",
        );
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function hasCrawlspace(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirst($leaf, $response, self::CURRENT, 'crawlspaces', 'area')) {
            return MappingResult::skipped('telt mee in has-crawlspace op de eerste kruipruimte');
        }

        // Only the yes is written. An empty list is not SmartTwin saying there is none — it models
        // crawlspaces explicitly but says nothing about leaving one out — and a wrong "nee" here
        // hides the crawlspace questions from the resident entirely.
        return MappingResult::mapped(
            'has-crawlspace',
            self::HAS_CRAWLSPACE_YES,
            'de response beschrijft een kruipruimte',
        );
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function crawlspaceHeight(Leaf $leaf, array $response): MappingResult
    {
        if ($this->notTheFirst($leaf, $response, self::CURRENT, 'crawlspaces', 'heightAboveGroundLevel')) {
            // More than one crawlspace is something neither the question nor the table covers, and
            // nothing in the response ranks them: their area is 0 in every dossier seen so far, so
            // there is nothing to weigh with. The first one answers.
            return MappingResult::skipped('has-crawlspace en crawlspace-height hangen aan de eerste kruipruimte');
        }

        $height = is_null($leaf->value) ? null : (float) $leaf->value;
        $order = CrawlspaceHeight::orderFor($height);
        $elementValueId = $this->elementValues->idForOrder('crawlspace', $order);

        if (is_null($elementValueId)) {
            return MappingResult::targetMissing(
                'crawlspace-height',
                $order,
                "geen element value met order {$order} voor crawlspace",
            );
        }

        return MappingResult::mapped(
            'crawlspace-height',
            $elementValueId,
            is_null($height)
                ? 'geen hoogte opgegeven, dus onbekend'
                : 'hoogte ' . abs($height) . ' m ten opzichte van maaiveld',
        );
    }

    /**
     * Whether another leaf of this path group carries the answer.
     *
     * @param  array<string, mixed>  $response
     */
    private function notTheFirst(Leaf $leaf, array $response, string $section, string $key, string $field): bool
    {
        foreach ($response[$section]['properties']['floorAssemblies'] ?? [] as $assembly => $parts) {
            foreach (array_keys($parts[$key] ?? []) as $part) {
                return $leaf->path !== "{$section}.properties.floorAssemblies.{$assembly}.{$key}.{$part}.{$field}";
            }
        }

        return true;
    }
}
