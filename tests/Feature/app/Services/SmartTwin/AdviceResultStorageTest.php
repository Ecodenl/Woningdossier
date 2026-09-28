<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Services\SmartTwin\AdviceResultStorage;
use Database\Seeders\FileTypeCategoriesTableSeeder;
use Database\Seeders\FileTypesTableSeeder;
use Database\Seeders\InputSourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AdviceResultStorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InputSourcesTableSeeder::class);
        $this->seed(FileTypeCategoriesTableSeeder::class);
        $this->seed(FileTypesTableSeeder::class);

        Storage::fake('downloads');
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, FileStorage> */
    private function rowsFor(Building $building)
    {
        return FileStorage::withExpired()
            ->allInputSources()
            ->where('building_id', $building->getKey())
            ->get();
    }

    public function test_stores_raw_json_and_a_single_file_storage_row(): void
    {
        $building = Building::factory()->create();

        $fileStorage = app(AdviceResultStorage::class)->storeRaw(
            $building,
            EventType::RESIDENT_SCAN_FINISHED,
            ['success' => true, 'current' => ['energyLabel' => 'C']],
        );

        $expectedFilename = "smarttwin/{$building->getKey()}/resident-advice.json";

        Storage::disk('downloads')->assertExists($expectedFilename);

        $this->assertSame($expectedFilename, $fileStorage->filename);
        $this->assertFalse($fileStorage->is_being_processed);
        $this->assertNotNull($fileStorage->available_until);
        $this->assertTrue($fileStorage->available_until->isFuture());
        $this->assertSame(InputSource::resident()->id, $fileStorage->input_source_id);
        $this->assertSame(
            FileType::findByShort(AdviceResultStorage::FILE_TYPE_SHORT)->id,
            $fileStorage->file_type_id,
        );

        $stored = json_decode(Storage::disk('downloads')->get($expectedFilename), true);
        $this->assertSame('C', $stored['current']['energyLabel']);
    }

    public function test_is_latest_wins_overwriting_the_same_row_and_file(): void
    {
        $building = Building::factory()->create();
        $storage = app(AdviceResultStorage::class);

        $storage->storeRaw($building, EventType::RESIDENT_SCAN_FINISHED, ['v' => 'first']);
        $storage->storeRaw($building, EventType::RESIDENT_SCAN_FINISHED, ['v' => 'second']);

        $this->assertCount(1, $this->rowsFor($building));

        $stored = json_decode(
            Storage::disk('downloads')->get("smarttwin/{$building->getKey()}/resident-advice.json"),
            true,
        );
        $this->assertSame('second', $stored['v']);
    }

    public function test_resident_and_coach_are_stored_as_separate_rows(): void
    {
        $building = Building::factory()->create();
        $storage = app(AdviceResultStorage::class);

        $storage->storeRaw($building, EventType::RESIDENT_SCAN_FINISHED, ['flow' => 'resident']);
        $storage->storeRaw($building, EventType::COACH_SCAN_FINISHED, ['flow' => 'coach']);

        $this->assertCount(2, $this->rowsFor($building));

        Storage::disk('downloads')->assertExists("smarttwin/{$building->getKey()}/resident-advice.json");
        Storage::disk('downloads')->assertExists("smarttwin/{$building->getKey()}/coach-advice.json");
    }
}
