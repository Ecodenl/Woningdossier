<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\ElementScope;
use App\Services\SmartTwin\Mapping\Elements\RoofShape;
use App\Services\SmartTwin\Mapping\Mappers\RoofInsulationMapper;
use App\Services\SmartTwin\Mapping\Mappers\RoofTypes;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use PHPUnit\Framework\TestCase;

/**
 * The figures are those of the first example response: a pitched roof of 39,75 m² at 47° and a flat
 * one of 30,73 m² at 1°, both at Rc 1,3, going to 4,79 and 4,22 in the scenario.
 */
final class RoofInsulationMapperTest extends TestCase
{
    private RoofInsulationMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->mapper();
    }

    private function mapper(ElementScope ...$advised): RoofInsulationMapper
    {
        return new RoofInsulationMapper(new FakeElementValues(), new FakeRoofTypes(), new FakeAdvisedElements(...$advised));
    }

    /**
     * @return array<string, mixed>
     */
    private function assembly(int $inclination, float $area, float $rcValue, string $id, int $roofType = 0): array
    {
        return [
            'inclination' => $inclination, 'orientation' => 2, 'windows' => [], 'area' => $area, 'id' => "assembly-{$id}",
            'roofs' => [[
                'roofType' => $roofType, 'insulationInConstruction' => false, 'insulationInterior' => false,
                'insulationExterior' => true, 'insulationThickness' => 0, 'rcValue' => $rcValue, 'area' => $area,
                'id' => $id, 'description' => 'Dak',
            ]],
            'description' => 'Dak',
        ];
    }

    private function response(array $current, array $scenario): array
    {
        return [
            'current'  => ['properties' => ['roofAssemblies' => $current]],
            'scenario' => ['properties' => ['roofAssemblies' => $scenario]],
        ];
    }

    private function example(): array
    {
        return $this->response(
            [$this->assembly(47, 39.75, 1.3, 'pitched', 2), $this->assembly(1, 30.73, 1.3, 'flat')],
            [$this->assembly(47, 39.75, 4.79, 'pitched', 2), $this->assembly(1, 30.73, 4.22, 'flat')],
        );
    }

    /** @return array<int, MappingResult> */
    private function mapClaimed(array $response): array
    {
        $claimed = array_flip($this->mapper->paths());
        $results = [];

        foreach ((new ResponseFlattener())->flatten($response) as $leaf) {
            if (isset($claimed[$leaf->pathGroup])) {
                $result = $this->mapper->map(new Building(), $leaf, $response);
                array_push($results, ...(is_array($result) ? $result : [$result]));
            }
        }

        return $results;
    }

    /** @return array<string, MappingResult> */
    private function mappedByTarget(array $response): array
    {
        $mapped = [];

        foreach ($this->mapClaimed($response) as $result) {
            if (MappingStatus::MAPPED === $result->status && MappingTarget::TOOL_QUESTION === $result->kind) {
                $mapped[$result->target] = $result;
            }
        }

        return $mapped;
    }

    /** @return array<int, string> */
    private function removedShapes(array $response): array
    {
        return array_values(array_map(
            fn (MappingResult $r) => $r->target,
            array_filter($this->mapClaimed($response), fn (MappingResult $r) => MappingTarget::ROOF_TYPE_REMOVAL === $r->kind),
        ));
    }

    public function test_both_shapes_get_their_own_surface_level_and_insulated_surface(): void
    {
        $mapped = $this->mappedByTarget($this->example());

        $this->assertEqualsWithDelta(39.75, $mapped['pitched-roof-surface']->value, 0.001);
        $this->assertEqualsWithDelta(30.73, $mapped['flat-roof-surface']->value, 0.001);
        $this->assertEqualsWithDelta(39.75, $mapped['pitched-roof-insulation-surface']->value, 0.001);
        $this->assertEqualsWithDelta(30.73, $mapped['flat-roof-insulation-surface']->value, 0.001);

        // Rc 1,3 is Matige isolatie on the roof table, calculate value 4 since Slecht sits at 3.
        $this->assertSame(FakeElementValues::OFFSET + 4, $mapped['is-pitched-roof-insulated']->value);
        $this->assertSame(FakeElementValues::OFFSET + 4, $mapped['is-flat-roof-insulated']->value);
        $this->assertSame(FakeElementValues::OFFSET + 4, $mapped['current-roof-insulation']->value);
        $this->assertSame([], $this->removedShapes($this->example()));
    }

    public function test_surfaces_keep_their_decimals(): void
    {
        $response = $this->response([$this->assembly(47, 39.7477841898, 1.3, 'p')], [$this->assembly(47, 39.7477841898, 1.3, 'p')]);

        $this->assertSame(39.75, $this->mappedByTarget($response)['pitched-roof-surface']->value);
    }

    public function test_ten_degrees_is_still_flat(): void
    {
        // Dossier 100261 has an assembly at exactly 10°.
        $response = $this->response([$this->assembly(10, 18.09, 0.89, 'a')], [$this->assembly(10, 18.09, 0.89, 'a')]);

        $mapped = $this->mappedByTarget($response);

        $this->assertArrayHasKey('flat-roof-surface', $mapped);
        $this->assertSame(['pitched'], $this->removedShapes($response));
    }

    public function test_the_main_roof_is_the_shape_with_the_most_surface(): void
    {
        $this->assertSame(FakeRoofTypes::id('pitched'), $this->mappedByTarget($this->example())['roof-type']->value);
    }

    public function test_a_shape_the_dwelling_does_not_have_is_removed_not_written_empty(): void
    {
        // Writing an empty surface to the flat roof would create its row — and with it a flat roof.
        $response = $this->response([$this->assembly(47, 39.75, 1.3, 'p')], [$this->assembly(47, 39.75, 1.3, 'p')]);

        $mapped = $this->mappedByTarget($response);

        $this->assertSame(['flat'], $this->removedShapes($response));
        $this->assertArrayNotHasKey('flat-roof-surface', $mapped);
        $this->assertArrayNotHasKey('flat-roof-insulation-surface', $mapped);
        $this->assertArrayNotHasKey('is-flat-roof-insulated', $mapped);
    }

    public function test_a_dwelling_without_a_roof_loses_both_shapes(): void
    {
        $response = $this->response([], []);

        $mapped = $this->mappedByTarget($response);

        $this->assertEqualsCanonicalizing(['flat', 'pitched'], $this->removedShapes($response));
        $this->assertNull($mapped['roof-type']->value);
        $this->assertNull($mapped['current-roof-insulation']->value);
    }

    public function test_a_scenario_that_leaves_a_shape_alone_empties_its_insulated_surface(): void
    {
        $response = $this->response(
            [$this->assembly(47, 39.75, 1.3, 'p'), $this->assembly(1, 30.73, 1.3, 'f')],
            [$this->assembly(47, 39.75, 4.79, 'p'), $this->assembly(1, 30.73, 1.3, 'f')],
        );

        $mapped = $this->mappedByTarget($response);

        $this->assertEqualsWithDelta(39.75, $mapped['pitched-roof-insulation-surface']->value, 0.001);
        $this->assertNull($mapped['flat-roof-insulation-surface']->value);
    }

    public function test_current_roof_types_is_never_written(): void
    {
        $this->assertArrayNotHasKey('current-roof-types', $this->mappedByTarget($this->example()));
    }

    public function test_every_answer_is_written_once(): void
    {
        $targets = array_map(fn (MappingResult $r) => $r->target, array_filter(
            $this->mapClaimed($this->example()),
            fn (MappingResult $r) => MappingStatus::MAPPED === $r->status,
        ));

        $this->assertSame(count($targets), count(array_unique($targets)));
    }

    public function test_solutions_for_both_shapes_carried_out_by_the_scenario_are_not_reported(): void
    {
        $this->mapper = $this->mapper(new ElementScope(Element::ROOF, RoofShape::FLAT), new ElementScope(Element::ROOF, RoofShape::PITCHED));

        $violations = array_filter($this->mapClaimed($this->example()), fn (MappingResult $r) => MappingStatus::SPEC_VIOLATION === $r->status);

        $this->assertSame([], $violations);
    }

    public function test_a_shape_insulated_without_its_own_solution_is_reported(): void
    {
        // A flat roof solution says nothing about the pitched roof the scenario also insulates.
        $this->mapper = $this->mapper(new ElementScope(Element::ROOF, RoofShape::FLAT));

        $violations = array_filter($this->mapClaimed($this->example()), fn (MappingResult $r) => MappingStatus::SPEC_VIOLATION === $r->status);

        $this->assertCount(1, $violations);
        $this->assertStringContainsString('hellend dak', reset($violations)->note);
    }

    public function test_nothing_a_roof_carries_is_left_unaccounted_for(): void
    {
        $notes = array_map(fn (MappingResult $r) => $r->note, $this->mapClaimed($this->example()));

        $this->assertNotContains('path group wordt geclaimd maar niet afgehandeld', $notes);
    }
}

final class FakeRoofTypes extends RoofTypes
{
    public static function id(string $short): int
    {
        return 'flat' === $short ? 702 : 701;
    }

    public function idFor(string $short): ?int
    {
        return self::id($short);
    }
}
