<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Mappers\CrawlspaceHeight;
use App\Services\SmartTwin\Mapping\Mappers\FloorInsulationMapper;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use PHPUnit\Framework\TestCase;

/**
 * The figures come from the two real dossiers: a concrete ground floor of 63,47 m² at Rc 0,15, and
 * one of 89,59 m² with a crawlspace half a metre below ground level.
 */
final class FloorInsulationMapperTest extends TestCase
{
    private FloorInsulationMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new FloorInsulationMapper(new FakeElementValues());
    }

    /**
     * @return array<string, mixed>
     */
    private function floor(float $area, float $rcValue): array
    {
        return ['area' => $area, 'rcValue' => $rcValue, 'floorType' => 2, 'insulationThickness' => 0,
                'cavity' => false, 'thermoPillows' => 0, 'description' => 'Vloer',
                'insulationInConstruction' => false, 'insulationInterior' => false, 'insulationExterior' => false];
    }

    /**
     * @return array<string, mixed>
     */
    private function crawlspace(float $height = -0.5): array
    {
        return ['area' => 0.0, 'description' => '', 'floorInsulation' => 0, 'floorRbf' => 0.0,
                'floorRbw' => 0.0, 'heightAboveGroundLevel' => $height, 'ventilation' => true];
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
                    fn (array $a) => ['area' => 0, 'floors' => $a[0], 'crawlspaces' => $a[1]],
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
                $results[] = $this->mapper->map(new Building(), $leaf, $response);
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

    public function test_it_answers_each_question_exactly_once(): void
    {
        // A crawlspace carries six fields, and every one of them is a leaf. Without an anchor the
        // yes gets written six times over.
        $results = $this->mapClaimed($this->response([[[$this->floor(63.47, 0.15)], [$this->crawlspace()]]]));

        $targets = array_map(
            fn (MappingResult $r) => $r->target,
            array_filter($results, fn (MappingResult $r) => MappingStatus::MAPPED === $r->status),
        );
        sort($targets);

        $this->assertSame(
            ['crawlspace-height', 'current-floor-insulation', 'floor-surface', 'has-crawlspace'],
            array_values($targets),
        );
    }

    public function test_the_floor_surface_is_the_floors_added_up(): void
    {
        $response = $this->response([
            [[$this->floor(40.0, 0.15)], []],
            [[$this->floor(23.47, 0.15)], []],
        ]);

        $this->assertEqualsWithDelta(63.47, $this->mappedByTarget($response)['floor-surface']->value, 0.001);
    }

    public function test_an_uninsulated_floor_reads_as_no_insulation(): void
    {
        // Rc 0,15 is what both real dossiers carry, and it sits under the 0,20 that starts Slecht.
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

    public function test_the_insulated_surface_is_what_the_scenario_improves(): void
    {
        $response = $this->response(
            [[[$this->floor(63.47, 0.15)], []]],
            [[[$this->floor(63.47, 3.5)], []]],
        );

        $this->assertEqualsWithDelta(63.47, $this->mappedByTarget($response)['insulation-floor-surface']->value, 0.001);
    }

    public function test_an_advice_without_floor_work_says_nothing_instead_of_zero(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(63.47, 0.15)], []]]));

        $this->assertArrayNotHasKey('insulation-floor-surface', $mapped);
        $this->assertArrayHasKey('floor-surface', $mapped);
    }

    public function test_a_described_crawlspace_answers_yes(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace()]]]));

        $this->assertSame('yes', $mapped['has-crawlspace']->value);
    }

    public function test_no_crawlspace_in_the_response_is_not_an_answer(): void
    {
        // The real coach dossier has an empty list. That is not SmartTwin saying there is none, and
        // a wrong "nee" hides the crawlspace questions from the resident entirely.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(63.47, 0.15)], []]]));

        $this->assertArrayNotHasKey('has-crawlspace', $mapped);
    }

    public function test_the_crawlspace_height_is_read_off_its_depth(): void
    {
        // Half a metre below ground, which is what the sample dossier carries — exactly on the
        // boundary, and the boundary belongs to the band above it.
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace(-0.5)]]]));

        $this->assertSame(FakeElementValues::ORDER_OFFSET + CrawlspaceHeight::HIGH, $mapped['crawlspace-height']->value);
    }

    public function test_a_shallow_crawlspace_reads_as_the_lowest_band(): void
    {
        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$this->crawlspace(-0.2)]]]));

        $this->assertSame(FakeElementValues::ORDER_OFFSET + CrawlspaceHeight::VERY_LOW, $mapped['crawlspace-height']->value);
    }

    public function test_a_crawlspace_without_a_height_reads_as_unknown(): void
    {
        $crawlspace = $this->crawlspace();
        $crawlspace['heightAboveGroundLevel'] = null;

        $mapped = $this->mappedByTarget($this->response([[[$this->floor(89.59, 0.15)], [$crawlspace]]]));

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
    }

    public function test_a_missing_insulation_level_is_reported_as_a_broken_mapping(): void
    {
        $elementValues = new FakeElementValues();
        $elementValues->missing = [3]; // Slechte isolatie
        $this->mapper = new FloorInsulationMapper($elementValues);

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
