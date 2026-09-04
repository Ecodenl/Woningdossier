<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Jobs\SmartTwin\ProcessAdviceResults;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\User;
use App\Services\SmartTwin\AdviceResultStorage;
use App\Services\SmartTwin\MappingReportStorage;
use App\Services\SmartTwin\SmartTwinFileTypes;
use App\Services\SmartTwin\SmartTwinService;
use Database\Seeders\FileTypeCategoriesTableSeeder;
use Database\Seeders\FileTypesTableSeeder;
use Database\Seeders\InputSourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class MappingReportStorageTest extends TestCase
{
    use RefreshDatabase;

    private const RESPONSE = [
        'current'   => ['calculationResult' => ['energyLabel' => 'C', 'heatDemand' => 42.5]],
        'solutions' => [['id' => 'a']],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InputSourcesTableSeeder::class);
        $this->seed(FileTypeCategoriesTableSeeder::class);
        $this->seed(FileTypesTableSeeder::class);

        Storage::fake('downloads');
    }

    private function building(): Building
    {
        $cooperation = Cooperation::factory()->create(['name' => 'Coöperatie Test']);
        $user = User::factory()->create(['cooperation_id' => $cooperation->id]);

        return Building::factory()->create([
            'user_id'     => $user->id,
            'postal_code' => '1234AB',
            'number'      => '12',
            'extension'   => 'A',
        ]);
    }

    private function mapAndStore(Building $building): FileStorage
    {
        app(AdviceResultStorage::class)->storeRaw($building, EventType::RESIDENT_SCAN_FINISHED, self::RESPONSE);

        return app(MappingReportStorage::class)->store(
            app(SmartTwinService::class)->processResults(
                $building,
                self::RESPONSE,
                EventType::RESIDENT_SCAN_FINISHED,
            ),
        );
    }

    private function csvFor(Building $building): string
    {
        return Storage::disk('downloads')->get("smarttwin/{$building->getKey()}/resident-mapping.csv");
    }

    public function test_it_writes_a_row_for_every_field_of_the_response(): void
    {
        $building = $this->building();

        $this->mapAndStore($building);

        $lines = array_values(array_filter(explode("\n", $this->csvFor($building))));

        // sep line + header + one row per leaf (energyLabel, heatDemand, solutions.0.id).
        $this->assertCount(5, $lines);
        $this->assertStringContainsString('current.calculationResult.energyLabel;', $this->csvFor($building));
        $this->assertStringContainsString('solutions.*.id;', $this->csvFor($building));
    }

    public function test_every_row_carries_its_status_and_what_that_status_means(): void
    {
        $building = $this->building();

        $this->mapAndStore($building);

        // Nothing is mapped yet, so this is the state the report starts in — and the file explains
        // that state without the documentation at hand.
        $this->assertStringContainsString('unmapped;"Geen mapping gedefinieerd voor dit veld"', $this->csvFor($building));
    }

    public function test_it_identifies_the_building_on_every_row(): void
    {
        $building = $this->building();

        $this->mapAndStore($building);
        $csv = $this->csvFor($building);

        $this->assertStringContainsString('Coöperatie Test', $csv);
        $this->assertStringContainsString('1234AB', $csv);
        $this->assertStringContainsString('12A', $csv);
    }

    public function test_excel_opens_it_the_same_way_in_any_locale(): void
    {
        $building = $this->building();

        $this->mapAndStore($building);

        $this->assertStringStartsWith("\u{FEFF}sep=;\n", $this->csvFor($building));
    }

    public function test_it_is_tracked_as_a_super_admin_only_artifact(): void
    {
        $building = $this->building();

        $fileStorage = $this->mapAndStore($building);

        $this->assertSame(
            FileType::findByShort(SmartTwinFileTypes::MAPPING_REPORT)->id,
            $fileStorage->file_type_id,
        );
        $this->assertSame($building->getKey(), $fileStorage->building_id);
        $this->assertNotNull($fileStorage->available_until);
    }

    public function test_replaying_the_mapping_overwrites_the_report_instead_of_piling_up(): void
    {
        $building = $this->building();
        $this->mapAndStore($building);

        // What the reprocess button on the super admin page does: re-read the stored response and
        // map it again. This is the loop the mapping gets built in.
        ProcessAdviceResults::dispatchSync($building->getKey(), EventType::RESIDENT_SCAN_FINISHED);

        $reports = FileStorage::withExpired()
            ->allInputSources()
            ->where('building_id', $building->getKey())
            ->whereHas('fileType', fn ($query) => $query->where('short', SmartTwinFileTypes::MAPPING_REPORT))
            ->get();

        $this->assertCount(1, $reports);
        $this->assertStringContainsString('current.calculationResult.energyLabel', $this->csvFor($building));
    }
}
