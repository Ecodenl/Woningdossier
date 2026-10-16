<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\Elements\AdvisedElements;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\RoofShape;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The roof of the roof insulation step.
 *
 * Hoomdossier asks the roof per shape — flat or pitched — and keeps a building_roof_types row for
 * each shape the dwelling has. Per shape this fills the surface, how well it is insulated, and the
 * surface the scenario insulates; for the roof as a whole the main roof type and its insulation. A
 * shape SmartTwin no longer describes has its row removed, which is also how current-roof-types
 * learns of it: that question reads the rows back.
 *
 * current-roof-types itself is never written. Saving it with two shapes through ToolQuestionService
 * overwrites the roof type of the row the first one made (see RoofAnswersTest), and the rows the
 * shape questions make answer it already.
 *
 * Like the facade and the floor, every answer is decided at one leaf, the anchor, and every other
 * leaf is skipped pointing at the answer it counts towards.
 */
class RoofInsulationMapper implements FieldMapper
{
    private const CURRENT = 'current';
    private const SCENARIO = 'scenario';

    private const ASSEMBLIES = '.properties.roofAssemblies';
    private const ROOF = self::ASSEMBLIES . '.*.roofs';

    private const NOTES = [
        'area'                     => 'telt mee in het dakoppervlak van zijn dakvorm',
        'rcValue'                  => 'telt mee in de isolatie van zijn dakvorm en van het hele dak',
        'id'                       => 'koppelt het dakdeel tussen current en scenario',
        // The construction — wooden beam layer, concrete floor, roof sheet — not the shape.
        'roofType'                 => 'constructie, geen dakvorm; de helling bepaalt plat of hellend',
        'insulationInConstruction' => 'zit in de Rc-waarde',
        'insulationInterior'       => 'zit in de Rc-waarde',
        'insulationExterior'       => 'zit in de Rc-waarde',
        'insulationThickness'      => 'zit in de Rc-waarde',
        'description'              => 'vrije omschrijving',
    ];

    public function __construct(
        private readonly ElementValues $elementValues,
        private readonly RoofTypes $roofTypes,
        private readonly AdvisedElements $advisedElements,
    )
    {
    }

    public function paths(): array
    {
        return array_merge(
            [
                self::CURRENT . self::ASSEMBLIES,
                self::CURRENT . self::ROOF,
                self::CURRENT . self::ASSEMBLIES . '.*.inclination',
                self::SCENARIO . self::ROOF . '.*.area',
                self::SCENARIO . self::ROOF . '.*.rcValue',
                self::SCENARIO . self::ROOF . '.*.id',
            ],
            array_map(fn (string $field) => self::CURRENT . self::ROOF . ".*.{$field}", array_keys(self::NOTES)),
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
        $current = RoofParts::fromResponse($response, self::CURRENT);
        $scenario = RoofParts::fromResponse($response, self::SCENARIO);

        // The order is not cosmetic; two observers rewrite the roof rows behind these answers.
        // Saving a different main roof type (BuildingFeatureHelper::performSecondaryRoofTypeLogic)
        // deletes every shape row and recreates one from the example building. Saving the insulation
        // of the whole roof (BuildingElementObserver) copies it onto every shape row. So both go
        // first, and the shapes are written over whatever they left.
        $results = [
            $this->roofType($current),
            $this->insulationLevel('current-roof-insulation', $current, 'het hele dak'),
        ];

        foreach (RoofShape::cases() as $shape) {
            array_push($results, ...$this->shape($response, $shape, $current->ofShape($shape), $scenario->ofShape($shape)));
        }

        return array_values(array_filter($results));
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, null|MappingResult>
     */
    private function shape(array $response, RoofShape $shape, RoofParts $current, RoofParts $scenario): array
    {
        $name = RoofShape::FLAT === $shape ? 'plat dak' : 'hellend dak';

        if ($current->totalArea() <= 0) {
            // Removing the row is what empties every answer about this shape; writing an empty
            // surface would create the row instead. Nothing else to write for it.
            return [MappingResult::roofTypeRemoved(
                $shape->roofTypeShort(),
                "de response beschrijft geen {$name} (helling " . (RoofShape::FLAT === $shape ? 't/m' : 'boven') . ' ' . RoofShape::FLAT_UP_TO_DEGREES . '°)',
            )];
        }

        $insulated = $scenario->improvedOver($current);

        return [
            // The surface first: it is what makes the row the other two are written to.
            MappingResult::mapped(
                "{$shape->value}-roof-surface",
                round($current->totalArea(), 2),
                "som van {$current->count()} dakdeel/-delen van het {$name}",
            ),
            $this->insulationLevel("is-{$shape->value}-roof-insulated", $current, "het {$name}"),
            $insulated->totalArea() > 0
                ? MappingResult::mapped(
                    "{$shape->value}-roof-insulation-surface",
                    round($insulated->totalArea(), 2),
                    "{$insulated->count()} dakdeel/-delen van het {$name} waarvan de Rc-waarde stijgt tussen current en scenario",
                )
                : MappingResult::cleared(
                    "{$shape->value}-roof-insulation-surface",
                    "het scenario verhoogt bij geen dakdeel van het {$name} de Rc-waarde",
                ),
            $this->disagreement($response, $shape, $insulated, $name),
        ];
    }

    private function insulationLevel(string $target, RoofParts $parts, string $what): MappingResult
    {
        $rcValue = $parts->weightedRcValue();

        if (is_null($rcValue)) {
            return MappingResult::cleared($target, "geen Rc-waarde te bepalen zonder oppervlak van {$what}");
        }

        $calculateValue = InsulationQuality::forRoof($rcValue);
        $elementValueId = $this->elementValues->idFor('roof-insulation', $calculateValue);

        if (is_null($elementValueId)) {
            // Most likely upgrade:extend-insulation-scales has not run here, so the roof scale has
            // no Slechte isolatie yet. A deploy that went wrong, not a gap in the mapping.
            return MappingResult::targetMissing(
                $target,
                $calculateValue,
                "geen element value met calculate_value {$calculateValue} voor roof-insulation",
            );
        }

        $label = $this->elementValues->labelFor($elementValueId) ?? "element value {$elementValueId}";

        return MappingResult::mapped(
            $target,
            $elementValueId,
            "oppervlakte-gewogen over {$what}: " . InsulationQuality::describeRoof($rcValue) . " → {$label}",
        );
    }

    /**
     * The main roof: the shape with the most surface. Hoomdossier also knows puntdak, afgerond dak and
     * rieten dak, which SmartTwin cannot tell apart from any other pitched roof.
     */
    private function roofType(RoofParts $parts): MappingResult
    {
        $shape = $parts->dominantShape();

        if (is_null($shape)) {
            return MappingResult::cleared('roof-type', 'de response beschrijft geen dak');
        }

        $roofTypeId = $this->roofTypes->idFor($shape->roofTypeShort());

        if (is_null($roofTypeId)) {
            return MappingResult::targetMissing('roof-type', $shape->roofTypeShort(), "geen roof type met short {$shape->roofTypeShort()}");
        }

        return MappingResult::mapped('roof-type', $roofTypeId, "{$shape->value}: de dakvorm met het grootste oppervlak");
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function disagreement(array $response, RoofShape $shape, RoofParts $insulated, string $name): ?MappingResult
    {
        $advised = $this->advisedElements->covers($response, Element::ROOF, $shape);
        $area = round($insulated->totalArea(), 2);

        return match (true) {
            $advised && $area <= 0 => MappingResult::specViolation("er is een solution voor het {$name}, maar het scenario verhoogt daar bij geen dakdeel de Rc-waarde"),
            ! $advised && $area > 0 => MappingResult::specViolation("het scenario verhoogt de Rc-waarde van {$area} m² {$name}, maar er is geen solution voor het {$name}"),
            default => null,
        };
    }

    private function noteFor(Leaf $leaf): string
    {
        return match (true) {
            str_starts_with($leaf->pathGroup, self::SCENARIO)                      => 'bepaalt het te isoleren dakoppervlak per dakvorm',
            $leaf->pathGroup === self::CURRENT . self::ASSEMBLIES                  => 'woning zonder dak: de dakvormen worden verwijderd',
            $leaf->pathGroup === self::CURRENT . self::ROOF                        => 'assembly zonder dakdelen',
            $leaf->pathGroup === self::CURRENT . self::ASSEMBLIES . '.*.inclination' => 'bepaalt of de dakdelen van deze assembly plat of hellend zijn',
            default                                                                => self::NOTES[$leaf->key()] ?? 'path group wordt geclaimd maar niet afgehandeld',
        };
    }
}
