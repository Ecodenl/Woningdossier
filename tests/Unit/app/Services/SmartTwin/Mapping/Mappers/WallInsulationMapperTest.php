<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Mappers\WallInsulationMapper;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use PHPUnit\Framework\TestCase;

/**
 * The numbers here come from a real coach dossier: three assemblies holding four closed facade
 * parts of 105,69 m² together, all cavity wall, all going from Rc 0,35 to 1,63 in the scenario.
 */
final class WallInsulationMapperTest extends TestCase
{
    private WallInsulationMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new WallInsulationMapper();
    }

    /**
     * @return array<string, mixed>
     */
    private function part(float $area, float $rcValue, int $facadeType = 1): array
    {
        return ['area' => $area, 'rcValue' => $rcValue, 'facadeType' => $facadeType, 'cavityThickness' => 50];
    }

    /**
     * @param  array<int, array<int, array<string, mixed>>>  $current
     * @param  null|array<int, array<int, array<string, mixed>>>  $scenario
     * @return array<string, mixed>
     */
    private function response(array $current, ?array $scenario = null): array
    {
        $section = fn (array $assemblies) => [
            'properties' => [
                'facadeAssemblies' => array_map(
                    fn (array $parts) => ['area' => 0, 'facades' => $parts, 'windows' => [], 'doors' => [], 'panels' => []],
                    $assemblies,
                ),
            ],
        ];

        return [
            'current'  => $section($current),
            'scenario' => $section($scenario ?? $current),
        ];
    }

    private function dossier(float $scenarioRc = 1.63): array
    {
        $current = [
            [$this->part(13.14, 0.35), $this->part(11.75, 0.35)],
            [$this->part(32.52, 0.35)],
            [$this->part(48.28, 0.35)],
        ];

        $scenario = [
            [$this->part(13.14, $scenarioRc), $this->part(11.75, $scenarioRc)],
            [$this->part(32.52, $scenarioRc)],
            [$this->part(48.28, $scenarioRc)],
        ];

        return $this->response($current, $scenario);
    }

    /**
     * Every leaf the mapper claims, run through it. Uses the real flattener, because which leaf
     * carries the answer is a property of the two together.
     *
     * @return array<int, MappingResult>
     */
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

    /**
     * @return array<string, MappingResult>
     */
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
        $results = $this->mapClaimed($this->dossier());

        $mapped = array_filter($results, fn (MappingResult $r) => MappingStatus::MAPPED === $r->status);

        // Four parts and two sections produce a stack of leaves; three answers come out.
        $this->assertCount(3, $mapped);
        $this->assertSame(
            ['has-cavity-wall', 'insulation-wall-surface', 'wall-surface'],
            $this->sortedTargets($mapped),
        );
    }

    /** @param array<int, MappingResult> $results */
    private function sortedTargets(array $results): array
    {
        $targets = array_map(fn (MappingResult $r) => $r->target, $results);
        sort($targets);

        return array_values($targets);
    }

    public function test_the_facade_surface_leaves_out_the_openings(): void
    {
        $mapped = $this->mappedByTarget($this->dossier());

        $this->assertEqualsWithDelta(105.69, $mapped['wall-surface']->value, 0.001);
    }

    public function test_the_insulated_surface_is_what_the_scenario_improves(): void
    {
        $mapped = $this->mappedByTarget($this->dossier());

        $this->assertEqualsWithDelta(105.69, $mapped['insulation-wall-surface']->value, 0.001);
    }

    public function test_only_the_improved_parts_count_towards_the_insulated_surface(): void
    {
        $response = $this->response(
            [[$this->part(30.0, 0.35), $this->part(20.0, 0.35)]],
            [[$this->part(30.0, 1.63), $this->part(20.0, 0.35)]],
        );

        $this->assertEqualsWithDelta(30.0, $this->mappedByTarget($response)['insulation-wall-surface']->value, 0.001);
    }

    public function test_an_advice_without_facade_work_says_nothing_instead_of_zero(): void
    {
        // Writing 0 would claim SmartTwin measured no surface to insulate, while it simply did not
        // advise a facade measure — and that number drives the cost and savings of one.
        $mapped = $this->mappedByTarget($this->dossier(scenarioRc: 0.35));

        $this->assertArrayNotHasKey('insulation-wall-surface', $mapped);
        $this->assertArrayHasKey('wall-surface', $mapped);
    }

    public function test_a_cavity_wall_answers_yes(): void
    {
        $this->assertSame(1, $this->mappedByTarget($this->dossier())['has-cavity-wall']->value);
    }

    public function test_a_solid_wall_answers_no(): void
    {
        $response = $this->response([[$this->part(50.0, 0.35, facadeType: 0)]]);

        $this->assertSame(2, $this->mappedByTarget($response)['has-cavity-wall']->value);
    }

    public function test_a_timber_frame_facade_answers_no(): void
    {
        $response = $this->response([[$this->part(50.0, 0.35, facadeType: 2)]]);

        $this->assertSame(2, $this->mappedByTarget($response)['has-cavity-wall']->value);
    }

    public function test_an_unknown_facade_type_is_reported_rather_than_guessed(): void
    {
        $response = $this->response([[$this->part(50.0, 0.35, facadeType: 7)]]);

        $statuses = array_map(fn (MappingResult $r) => $r->status, $this->mapClaimed($response));

        $this->assertContains(MappingStatus::VALUE_UNMAPPED, $statuses);
    }

    public function test_the_answer_hangs_off_the_first_part_of_the_response_not_the_first_assembly(): void
    {
        // A wall that is all window has no closed parts at all, and in a real response that is the
        // first assembly. Counting from index 0 would leave every question unanswered.
        $response = $this->response([
            [],
            [$this->part(26.22, 0.35)],
            [],
        ]);

        $mapped = $this->mappedByTarget($response);

        $this->assertEqualsWithDelta(26.22, $mapped['wall-surface']->value, 0.001);
        $this->assertSame(1, $mapped['has-cavity-wall']->value);
    }

    public function test_an_assembly_without_closed_parts_is_reported_as_understood(): void
    {
        $response = $this->response([[], [$this->part(26.22, 0.35)]]);

        $notes = array_map(
            fn (MappingResult $r) => $r->note,
            array_filter($this->mapClaimed($response), fn (MappingResult $r) => MappingStatus::SKIPPED === $r->status),
        );

        $this->assertContains('assembly zonder dichte geveldelen', $notes);
    }

    public function test_a_facade_without_surface_is_reported_rather_than_written_as_zero(): void
    {
        $response = $this->response([[$this->part(0.0, 0.35)]]);

        $results = array_filter(
            $this->mapClaimed($response),
            fn (MappingResult $r) => MappingStatus::VALUE_UNMAPPED === $r->status,
        );

        $this->assertNotEmpty($results);
    }

    public function test_the_cavity_thickness_is_deliberately_left_alone(): void
    {
        // It drops to 0 in the scenario once the cavity is filled, so it cannot answer whether the
        // dwelling has one. facadeType can, and does.
        $response = $this->response([[$this->part(50.0, 0.35)]]);

        $notes = array_map(fn (MappingResult $r) => $r->note, $this->mapClaimed($response));

        $this->assertContains('facadeType is het signaal voor has-cavity-wall', $notes);
    }
}
