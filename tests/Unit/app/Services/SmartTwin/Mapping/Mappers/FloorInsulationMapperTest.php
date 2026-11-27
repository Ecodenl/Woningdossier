<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\ElementScope;
use App\Services\SmartTwin\Mapping\Mappers\CrawlspaceHeight;
use App\Services\SmartTwin\Mapping\Mappers\FloorInsulationMapper;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use PHPUnit\Framework\TestCase;

/**
 * The figures come from the real dossiers: a concrete ground floor of 63,47 m² at Rc 0,15, one of
 * 89,59 m² with a crawlspace half a metre below ground level, and one of 58,12 m² at Rc 0,52 with a
 * floor solution the scenario does not carry out.
 */
final class FloorInsulationMapperTest extends TestCase
{
    private FloorInsulationMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new FloorInsulationMapper(new FakeElementValues(), new FakeAdvisedElements());
    }

    /**
     * @return array<string, mixed>
     */
    private function floor(float $area, float $rcValue, string $id = 'floor-1'): array
    {
        return ['floorType' => 2, 'insulationInConstruction' => false, 'insulationInterior' => false,
                'insulationExterior' => false, 'rcValue' => $rcValue, 'insulationThickness' => 0,
                'cavity' => false, 'thermoPillows' => 0, 'area' => $area, 'id' => $id, 'description' => 'Vloer'];
    }

    /**
     * @return array<string, mixed>
     */
    private function crawlspace(?float $height = -0.5): array
    {
        return ['floorInsulation' => 0, 'floorRbf' => 0.0, 'floorRbw' => 0.0, 'heightAboveGroundLevel' => $height,
                'ventilation' => true, 'area' => 0.0, 'id' => 'crawlspace-1', 'description' => ''];
    }

    /**
     * @param  array<int, array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}>  $current
     * @return array<string, mixed>
     */
    private function response(array $current, ?array $scenario = null): array
    {
        $section = fn (array $assemblies) => [
            'properties' => [
                'floorAssemblies' => array_map(
                    fn (array $a) => ['floors' => $a[0], 'crawlspaces' => $a[1], 'area' => 0, 'id' => 'assembly', 'description' => 'HOR'],
                    $assemblies,
                ),
            ],
        ];

        return ['current' => $section($current), 'scenario' => $section($scenario ?? $current)];
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
            if (MappingStatus::MAPPED === $result->status) {
                $mapped[$result->target] = $result;
            }
        }

        return $mapped;
    }

    /** @return array<int, MappingResult> */
    private function violations(array $response): array
    {
        return array_values(array_filter(
            $this->mapClaimed($response),
            fn (MappingResult $r) => MappingStatus::SPEC_VIOLATION === $r->status,
        ));
    }

    public function test_it_answers_each_question_exactly_once(): void
    {
        // A crawlspace carries eight fields and a floor eleven, and every one of them is a leaf. Each
        // answer is still written once.
        $results = $this->mapClaimed($this->response([[[$this->floor(63.47, 0.15)], [$this->crawlspace()]]]));

        $targets = array_map(
            fn (MappingResult $r) => $r->target,
            array_filter($results, fn (MappingResult $r) => MappingStatus::MAPPED === $r->status),
        );
        sort($targets);

        $this->assertSame(
            ['crawlspace-height', 'current-floor-insulation', 'floor-surface', 'has-crawlspace', 'insulation-floor-surface'],
            array_values($targets),
        );
    }

    public function test_the_floor_surface_is_the_floors_added_up(): void
    {
        $response = $this->response([
            [[$this->floor(40.0, 0.15, 'a')], []],
            [[$this->floor(23.47, 0.15, 'b')], []],
        ]);

        $this->assertEqualsWithDelta(63.47, $this->mappedByTarget($response)['floor-surface']->value, 0.001);
    }

    public function test_an_uninsulated_floor_reads_as_no_insulation(): void
    {
        // Rc 0,15 is what two real dossiers carry, and it sits under the 0,20 that starts Slecht.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(63.47, 0.15)], []]]));

        $this->assertSame(FakeElementValues::OFFSET + 2, $mapped['current-floor-insulation']->value);
    }

    public function test_a_poorly_insulated_floor_reads_as_the_level_the_wall_does_not_have(): void
    {
        // Rc 0,5 is Slechte isolatie for a floor. The wall scale has no such level, which is why
        // the two run on their own tables.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(63.47, 0.5)], []]]));

        $this->assertSame(FakeElementValues::OFFSET + 3, $mapped['current-floor-insulation']->value);
    }

    public function test_the_insulation_note_quotes_the_floor_table(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(63.47, 0.65)], []]]));

        $this->assertSame(
            'oppervlakte-gewogen Rc 0,65 valt tussen 0,20 en 1,00 → optie 903',
            $mapped['current-floor-insulation']->note,
        );
    }

    public function test_the_insulated_surface_is_what_the_scenario_improves(): void
    {
        $response = $this->response(
            [[[$this->floor(63.47, 0.15)], []]],
            [[[$this->floor(63.47, 3.5)], []]],
        );

        $this->assertEqualsWithDelta(63.47, $this->mappedByTarget($response)['insulation-floor-surface']->value, 0.001);
    }

    public function test_a_scenario_without_floor_work_empties_the_insulated_surface_instead_of_writing_zero(): void
    {
        // A previous scan may have written one. Leaving it would contradict this scan; 0 would claim
        // something SmartTwin did not say and feed a cost calculation.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(63.47, 0.15)], []]]));

        $this->assertArrayHasKey('insulation-floor-surface', $mapped);
        $this->assertNull($mapped['insulation-floor-surface']->value);
    }

    public function test_a_dwelling_without_floors_empties_the_floor_answers(): void
    {
        $mapped = $this->mappedByTarget(['current' => ['properties' => ['floorAssemblies' => []]],
                                         'scenario' => ['properties' => ['floorAssemblies' => []]]]);

        $this->assertNull($mapped['floor-surface']->value);
        $this->assertNull($mapped['current-floor-insulation']->value);
        $this->assertNull($mapped['insulation-floor-surface']->value);
        $this->assertSame('no', $mapped['has-crawlspace']->value);
        $this->assertNull($mapped['crawlspace-height']->value);
    }

    public function test_a_described_crawlspace_answers_yes(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace()]]]));

        $this->assertSame('yes', $mapped['has-crawlspace']->value);
    }

    public function test_a_crawlspace_above_ground_level_counts_too(): void
    {
        // Only the size of the height matters.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace(0.4)]]]));

        $this->assertSame('yes', $mapped['has-crawlspace']->value);
        $this->assertSame(FakeElementValues::ORDER_OFFSET + CrawlspaceHeight::LOW, $mapped['crawlspace-height']->value);
    }

    public function test_no_crawlspace_in_the_response_answers_no(): void
    {
        // SmartTwin describes the dwelling: a crawlspace it leaves out is one the dwelling does not
        // have, and the import takes the description over as a whole.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(58.12, 0.52)], []]]));

        $this->assertSame('no', $mapped['has-crawlspace']->value);
        $this->assertNull($mapped['crawlspace-height']->value);
    }

    public function test_a_crawlspace_at_ground_level_answers_no(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(58.12, 0.52)], [$this->crawlspace(0.0)]]]));

        $this->assertSame('no', $mapped['has-crawlspace']->value);
        $this->assertNull($mapped['crawlspace-height']->value);
    }

    public function test_the_crawlspace_height_is_read_off_its_depth(): void
    {
        // Half a metre below ground, which is what the sample dossier carries — exactly on the
        // boundary, and the boundary belongs to the band above it.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace(-0.5)]]]));

        $this->assertSame(FakeElementValues::ORDER_OFFSET + CrawlspaceHeight::HIGH, $mapped['crawlspace-height']->value);
    }

    public function test_the_height_note_names_the_band_and_the_option(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace(-0.5)]]]));

        $this->assertSame(
            'hoogte 0,50 m t.o.v. maaiveld valt vanaf 0,50 m → optie 800',
            $mapped['crawlspace-height']->note,
        );
    }

    public function test_a_shallow_crawlspace_reads_as_the_lowest_band(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace(-0.2)]]]));

        $this->assertSame(FakeElementValues::ORDER_OFFSET + CrawlspaceHeight::VERY_LOW, $mapped['crawlspace-height']->value);
    }

    public function test_a_crawlspace_without_a_height_reads_as_unknown(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace(null)]]]));

        $this->assertSame('unknown', $mapped['has-crawlspace']->value);
        $this->assertSame(FakeElementValues::ORDER_OFFSET + CrawlspaceHeight::UNKNOWN, $mapped['crawlspace-height']->value);
    }

    public function test_the_height_comes_from_one_crawlspace_even_when_there_are_several(): void
    {
        $response = $this->response([[
            [$this->floor(89.59, 0.15)],
            [$this->crawlspace(-0.8), $this->crawlspace(-0.2)],
        ]]);

        $heights = array_filter(
            $this->mapClaimed($response),
            fn (MappingResult $r) => 'crawlspace-height' === $r->target && MappingStatus::MAPPED === $r->status,
        );

        $this->assertCount(1, $heights);
        $this->assertSame(FakeElementValues::ORDER_OFFSET + CrawlspaceHeight::HIGH, reset($heights)->value);
    }

    public function test_a_floor_solution_the_scenario_does_not_carry_out_is_reported(): void
    {
        // The first example response: Vloerisolatie begane grond, € 3.613, and a floor that stays at
        // Rc 0,52 in the scenario. Nothing is written for it; the answers follow the properties.
        $this->mapper = new FloorInsulationMapper(new FakeElementValues(), new FakeAdvisedElements(new ElementScope(Element::FLOOR)));

        $response = $this->response([[[$this->floor(58.12, 0.52)], [$this->crawlspace()]]]);

        $this->assertCount(1, $this->violations($response));
        $this->assertNull($this->mappedByTarget($response)['insulation-floor-surface']->value);
    }

    public function test_a_floor_the_scenario_insulates_without_a_solution_is_reported(): void
    {
        $response = $this->response(
            [[[$this->floor(63.47, 0.15)], []]],
            [[[$this->floor(63.47, 3.5)], []]],
        );

        $this->assertCount(1, $this->violations($response));
        $this->assertEqualsWithDelta(63.47, $this->mappedByTarget($response)['insulation-floor-surface']->value, 0.001);
    }

    public function test_a_solution_carried_out_by_the_scenario_is_not_reported(): void
    {
        $this->mapper = new FloorInsulationMapper(new FakeElementValues(), new FakeAdvisedElements(new ElementScope(Element::FLOOR)));

        $response = $this->response(
            [[[$this->floor(63.47, 0.15)], []]],
            [[[$this->floor(63.47, 3.5)], []]],
        );

        $this->assertSame([], $this->violations($response));
    }

    public function test_a_missing_insulation_level_is_reported_as_a_broken_mapping(): void
    {
        $elementValues = new FakeElementValues();
        $elementValues->missing = [3]; // Slechte isolatie
        $this->mapper = new FloorInsulationMapper($elementValues, new FakeAdvisedElements());

        $results = array_filter(
            $this->mapClaimed($this->response([[[$this->floor(63.47, 0.5)], []]])),
            fn (MappingResult $r) => MappingStatus::TARGET_MISSING === $r->status,
        );

        $this->assertCount(1, $results);
        $this->assertSame('current-floor-insulation', reset($results)->target);
    }

    public function test_nothing_a_floor_carries_is_left_unaccounted_for(): void
    {
        $notes = array_map(
            fn (MappingResult $r) => $r->note,
            $this->mapClaimed($this->response([[[$this->floor(63.47, 0.15)], [$this->crawlspace()]]])),
        );

        $this->assertNotContains('path group wordt geclaimd maar niet afgehandeld', $notes);
    }
}
