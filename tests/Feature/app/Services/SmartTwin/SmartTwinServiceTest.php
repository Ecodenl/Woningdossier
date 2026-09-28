<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MapperRegistry;
use App\Services\SmartTwin\Mapping\MappingApplier;
use App\Services\SmartTwin\Mapping\MappingReport;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\SmartTwinService;
use Database\Seeders\InputSourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * The promise these tests hold on to: the walk runs over the response, so the report is complete
 * whatever the registry holds, and a field leaves the unmapped rows exactly when — and only when —
 * a mapper claims its path group. That is what makes the mapping buildable in increments.
 *
 * The applier is stubbed here on purpose. What it does with a result is its own test
 * (MappingApplierTest); what matters here is that the walk hands it the right ones.
 */
final class SmartTwinServiceTest extends TestCase
{
    use RefreshDatabase;

    private const RESPONSE = [
        'current'   => ['calculationResult' => ['energyLabel' => 'C', 'heatDemand' => 42.5]],
        'solutions' => [['id' => 'a'], ['id' => 'b']],
    ];

    private RecordingApplier $applier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InputSourcesTableSeeder::class);

        $this->applier = new RecordingApplier();
        $this->app->instance(MappingApplier::class, $this->applier);
    }

    private function process(array $mappers = []): MappingReport
    {
        $this->app->instance(MapperRegistry::class, new MapperRegistry($mappers));

        return $this->app->make(SmartTwinService::class)->processResults(
            Building::factory()->create(),
            self::RESPONSE,
            EventType::RESIDENT_SCAN_FINISHED,
        );
    }

    /** @return array<string, MappingStatus> */
    private function statusesByPath(MappingReport $report): array
    {
        $statuses = [];
        foreach ($report->entries() as $entry) {
            $statuses[$entry->leaf->path] = $entry->status;
        }

        return $statuses;
    }

    public function test_without_mappers_every_field_is_reported_as_unmapped(): void
    {
        $report = $this->process();

        $this->assertCount(4, $report->entries());
        $this->assertSame(4, $report->counts()[MappingStatus::UNMAPPED->value]);
        $this->assertSame(0, $report->counts()[MappingStatus::MAPPED->value]);
        $this->assertSame([], $this->applier->applied);
    }

    public function test_a_registered_mapper_takes_its_field_out_of_the_unmapped_rows(): void
    {
        $report = $this->process([EnergyLabelMapper::class]);

        $statuses = $this->statusesByPath($report);

        $this->assertSame(MappingStatus::MAPPED, $statuses['current.calculationResult.energyLabel']);
        // Everything the mapper does not claim keeps being reported, so the report never shrinks
        // to only what we happen to understand.
        $this->assertSame(MappingStatus::UNMAPPED, $statuses['current.calculationResult.heatDemand']);
        $this->assertCount(4, $report->entries());
    }

    public function test_a_mapper_claims_every_item_of_a_list_through_one_path_group(): void
    {
        $report = $this->process([SolutionIdMapper::class]);

        $statuses = $this->statusesByPath($report);

        $this->assertSame(MappingStatus::SKIPPED, $statuses['solutions.0.id']);
        $this->assertSame(MappingStatus::SKIPPED, $statuses['solutions.1.id']);
    }

    public function test_a_failing_mapper_does_not_take_the_rest_of_the_run_down(): void
    {
        $report = $this->process([ExplodingMapper::class]);

        $statuses = $this->statusesByPath($report);

        $this->assertSame(MappingStatus::ERROR, $statuses['current.calculationResult.energyLabel']);
        $this->assertSame(MappingStatus::UNMAPPED, $statuses['current.calculationResult.heatDemand']);
        $this->assertCount(1, $report->entriesNeedingAttention());
    }

    public function test_a_mapped_result_is_handed_to_the_applier_with_its_value(): void
    {
        $this->process([EnergyLabelMapper::class]);

        $this->assertSame(
            [['target' => 'energy-label', 'value' => 'C', 'input_source' => InputSource::RESIDENT_SHORT]],
            $this->applier->applied,
        );
    }

    public function test_results_that_are_not_mapped_reach_the_applier_but_write_nothing(): void
    {
        // The walk does not decide what is writable; the applier does. It is handed every result
        // and passes the ones it cannot write straight through.
        $this->process([SolutionIdMapper::class]);

        $this->assertSame([], $this->applier->applied);
        $this->assertSame(2, $this->applier->seen);
    }

    public function test_the_report_records_what_the_applier_made_of_a_result(): void
    {
        // A mapping pointing at a short that does not exist is a bug in the mapping, and the report
        // has to say so rather than claim the field was mapped.
        $this->app->instance(MappingApplier::class, new DowngradingApplier());

        $report = $this->process([EnergyLabelMapper::class]);

        $statuses = $this->statusesByPath($report);

        $this->assertSame(MappingStatus::TARGET_MISSING, $statuses['current.calculationResult.energyLabel']);
        $this->assertCount(1, $report->entriesNeedingAttention());
    }

    public function test_a_write_that_throws_is_reported_as_an_error_and_not_as_mapped(): void
    {
        $this->app->instance(MappingApplier::class, new ExplodingApplier());

        $report = $this->process([EnergyLabelMapper::class]);

        $statuses = $this->statusesByPath($report);

        $this->assertSame(MappingStatus::ERROR, $statuses['current.calculationResult.energyLabel']);
        $this->assertSame(0, $report->counts()[MappingStatus::MAPPED->value]);
    }
}

/**
 * Records what it was asked to write instead of writing it, so the walk can be tested without a
 * seeded database. Passes results through unchanged, like the real applier does on success.
 */
class RecordingApplier extends MappingApplier
{
    /** @var array<int, array{target: string, value: mixed, input_source: string}> */
    public array $applied = [];

    public int $seen = 0;

    public function apply(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        ++$this->seen;

        if (MappingStatus::MAPPED === $result->status) {
            $this->applied[] = [
                'target'       => $result->target,
                'value'        => $result->value,
                'input_source' => $inputSource->short,
            ];
        }

        return $result;
    }
}

final class DowngradingApplier extends MappingApplier
{
    public function apply(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        return MappingResult::targetMissing($result->target, $result->value);
    }
}

final class ExplodingApplier extends MappingApplier
{
    public function apply(Building $building, InputSource $inputSource, MappingResult $result): MappingResult
    {
        throw new RuntimeException('schrijven mislukt');
    }
}

final class EnergyLabelMapper implements FieldMapper
{
    public function paths(): array
    {
        return ['current.calculationResult.energyLabel'];
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        return MappingResult::mapped('energy-label', $leaf->value);
    }
}

final class SolutionIdMapper implements FieldMapper
{
    public function paths(): array
    {
        return ['solutions.*.id'];
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        return MappingResult::skipped('identifier');
    }
}

final class ExplodingMapper implements FieldMapper
{
    public function paths(): array
    {
        return ['current.calculationResult.energyLabel'];
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        throw new RuntimeException('kapot');
    }
}
