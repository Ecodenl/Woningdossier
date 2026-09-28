<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Mappers\SolutionMeasures;
use App\Services\SmartTwin\Mapping\Mappers\SolutionsMapper;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use PHPUnit\Framework\TestCase;

/**
 * The figures come from a real coach dossier: a cavity insulation of € 1.622,65 made up of primary
 * costs only, and a pitched roof where € 4.254,96 of primary cost sits next to € 522,26 of optional
 * additional cost — the case that decides whether the price a resident sees is the right one.
 */
final class SolutionsMapperTest extends TestCase
{
    private SolutionsMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        // Which measure a solution is comes from the mappings table; SolutionMeasuresTest covers
        // that lookup. Stubbed here so these stay database-free.
        $this->mapper = new SolutionsMapper(new FakeSolutionMeasures());
    }

    /**
     * @param  array<int, array<string, mixed>>  $solutions
     * @return array<string, mixed>
     */
    private function response(array $solutions): array
    {
        return [
            'scenario' => [
                'solutions'                 => $solutions,
                'totalCostWithTax'          => array_sum(array_map(fn ($s) => $s['adviceSolutionCost']['totalCostWithTax'] ?? 0, $solutions)),
                'energyCostSavingsPerMonth' => 114,
            ],
            'solutions' => [],
        ];
    }

    /**
     * @param  array<int, float>  $primary
     * @param  array<int, float>  $additional
     * @return array<string, mixed>
     */
    private function solution(string $id, array $primary, array $additional = []): array
    {
        $cost = fn (float $amount) => ['code' => 'NL21', 'title' => 't', 'description' => 'd', 'costWithTax' => $amount, 'quantity' => 1, 'unit' => 'm2'];

        return [
            'id'                 => $id,
            'name'               => 'Naam bij SmartTwin',
            'details'            => [['id' => 'lambda', 'displayName' => 'Lambda', 'value' => 0.035, 'unit' => 'W/(mK)']],
            'adviceSolutionCost' => [
                'totalCostWithTax' => array_sum($primary) + array_sum($additional),
                'primaryCosts'     => array_map($cost, $primary),
                'additionalCosts'  => array_map($cost, $additional),
                'exceptionalCosts' => [],
            ],
        ];
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

    /** @return array<int, MappingResult> */
    private function advices(array $response): array
    {
        return array_values(array_filter(
            $this->mapClaimed($response),
            fn (MappingResult $r) => MappingStatus::MAPPED === $r->status,
        ));
    }

    public function test_every_solution_becomes_its_own_entry(): void
    {
        $advices = $this->advices($this->response([
            $this->solution('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls', [1439.21, 150.80, 32.64]),
            $this->solution('Insulate Pitched Roof Inside|SmartTwin:Pitched_Roof_Interior_Insulation_PIR_120', [4254.96]),
        ]));

        $this->assertCount(2, $advices);
        $this->assertSame(
            ['cavity-wall-insulation', 'roof-insulation-pitched-inside'],
            array_map(fn (MappingResult $r) => $r->target, $advices),
        );
    }

    public function test_an_advised_measure_is_not_written_as_a_tool_question(): void
    {
        $advices = $this->advices($this->response([
            $this->solution('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls', [1622.65]),
        ]));

        $this->assertSame(MappingTarget::ADVICE, $advices[0]->kind);
    }

    public function test_the_price_is_the_primary_costs_added_up(): void
    {
        $advices = $this->advices($this->response([
            $this->solution('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls', [1439.21, 150.80, 32.64]),
        ]));

        $this->assertEqualsWithDelta(1622.65, $advices[0]->value, 0.001);
    }

    public function test_optional_extras_stay_out_of_the_price(): void
    {
        // The pitched roof of the real dossier: € 522,26 of "Isolatie verwijderen" that a coach may
        // or may not have included. totalCostWithTax would have quoted € 4.777,22.
        $advices = $this->advices($this->response([
            $this->solution('Insulate Pitched Roof Inside|SmartTwin:Pitched_Roof_Interior_Insulation_PIR_120', [4254.96], [522.26]),
        ]));

        $this->assertEqualsWithDelta(4254.96, $advices[0]->value, 0.001);
    }

    public function test_a_solution_we_have_no_coupling_for_is_reported_with_its_id(): void
    {
        // Not an error: SmartTwin adds products. The report has to name the id, because that is
        // what somebody types into the mappings table to fix it.
        $results = $this->mapClaimed($this->response([
            $this->solution('Install Heat Pump|SmartTwin:Nog_Niet_Gekoppeld', [5000.0]),
        ]));

        $unmapped = array_values(array_filter($results, fn (MappingResult $r) => MappingStatus::VALUE_UNMAPPED === $r->status));

        $this->assertCount(1, $unmapped);
        $this->assertStringContainsString('Install Heat Pump|SmartTwin:Nog_Niet_Gekoppeld', $unmapped[0]->note);
    }

    public function test_a_solution_without_primary_costs_produces_no_entry(): void
    {
        $advices = $this->advices($this->response([
            $this->solution('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls', []),
        ]));

        $this->assertSame([], $advices);
    }

    public function test_the_package_saving_is_recorded_as_not_belonging_to_a_measure(): void
    {
        // SmartTwin gives one figure for the whole package while the woonplan shows a saving per
        // card. The report says so rather than leaving the field looking forgotten.
        $notes = array_map(fn (MappingResult $r) => $r->note, $this->mapClaimed($this->response([
            $this->solution('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls', [1622.65]),
        ])));

        $this->assertContains('besparing geldt het hele pakket, niet een losse maatregel', $notes);
    }

    public function test_nothing_a_solution_carries_is_left_unaccounted_for(): void
    {
        // Every leaf of a solution is claimed, so the report never suggests a field was overlooked
        // when it was in fact considered and set aside.
        $results = $this->mapClaimed($this->response([
            $this->solution('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls', [1439.21], [522.26]),
        ]));

        $notes = array_map(fn (MappingResult $r) => $r->note, $results);

        $this->assertNotContains('path group wordt geclaimd maar niet afgehandeld', $notes);
    }
}

/**
 * Couples the two solutions the real dossiers use and nothing else, so an uncoupled product is a
 * case these tests can exercise rather than an accident.
 */
final class FakeSolutionMeasures extends SolutionMeasures
{
    public function shortFor(string $solutionId): ?string
    {
        return match (true) {
            str_contains($solutionId, 'Cavity')  => 'cavity-wall-insulation',
            str_contains($solutionId, 'Pitched') => 'roof-insulation-pitched-inside',
            default                              => null,
        };
    }
}
