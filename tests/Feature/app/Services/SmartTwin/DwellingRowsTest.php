<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingRoofType;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\RoofType;
use App\Models\User;
use App\Services\SmartTwin\DwellingRows;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Before SmartTwin writes in an input source, that input source's rows about the dwelling are made
 * equal to master, so SmartTwin's answers land on top of the dossier instead of replacing it
 * (decision 2026-10-15-smarttwin-import-overschrijft-master, M1).
 */
final class DwellingRowsTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Building $building;
    private InputSource $resident;
    private InputSource $coach;
    private InputSource $master;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = User::first()->building;
        $this->resident = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->coach = InputSource::findByShort(InputSource::COACH_SHORT);
        $this->master = InputSource::findByShort(InputSource::MASTER_SHORT);

        foreach (DwellingRows::TABLES as $table) {
            DB::table($table)->where('building_id', $this->building->id)->delete();
        }

        // The resident filled these in; GetMyValuesTrait copied them to master.
        BuildingFeature::create(['building_id' => $this->building->id, 'input_source_id' => $this->resident->id, 'build_year' => 1975, 'monument' => 1]);
        BuildingElement::create(['building_id' => $this->building->id, 'input_source_id' => $this->resident->id, 'element_id' => Element::findByShort('crawlspace')->id, 'extra' => ['access' => 'yes']]);
        BuildingRoofType::create(['building_id' => $this->building->id, 'input_source_id' => $this->resident->id, 'roof_type_id' => RoofType::findByShort('pitched')->id, 'roof_surface' => 40]);
    }

    /**
     * @return array<string, int>
     */
    private function counts(InputSource $inputSource): array
    {
        return collect(DwellingRows::TABLES)->mapWithKeys(fn (string $table) => [
            $table => DB::table($table)->where('building_id', $this->building->id)->where('input_source_id', $inputSource->id)->count(),
        ])->all();
    }

    private function feature(InputSource $inputSource): BuildingFeature
    {
        return BuildingFeature::withoutGlobalScopes()->where('building_id', $this->building->id)->where('input_source_id', $inputSource->id)->firstOrFail();
    }

    public function test_an_input_source_without_rows_gets_masters(): void
    {
        app(DwellingRows::class)->equalizeToMaster($this->building, $this->coach);

        $this->assertSame($this->counts($this->master), $this->counts($this->coach));
        $this->assertSame(1975, (int) $this->feature($this->coach)->build_year);
        $this->assertSame(['access' => 'yes'], BuildingElement::withoutGlobalScopes()->where('building_id', $this->building->id)->where('input_source_id', $this->coach->id)->first()->extra);
    }

    public function test_an_input_source_with_rows_of_its_own_gets_masters_instead(): void
    {
        BuildingFeature::withoutGlobalScopes()->where('building_id', $this->building->id)->where('input_source_id', $this->coach->id)->delete();
        DB::table('building_features')->insert(['building_id' => $this->building->id, 'input_source_id' => $this->coach->id, 'build_year' => 1930]);

        app(DwellingRows::class)->equalizeToMaster($this->building, $this->coach);

        $this->assertSame(1, $this->counts($this->coach)['building_features']);
        $this->assertSame(1975, (int) $this->feature($this->coach)->build_year);
    }

    public function test_master_stays_as_it_was(): void
    {
        $before = $this->feature($this->master)->getAttributes();

        app(DwellingRows::class)->equalizeToMaster($this->building, $this->coach);

        $this->assertSame($before, $this->feature($this->master)->getAttributes());
        $this->assertSame(['building_features' => 1, 'building_elements' => 1, 'building_roof_types' => 1], $this->counts($this->master));
    }

    public function test_a_table_master_has_no_rows_in_is_left_alone(): void
    {
        DB::table('building_roof_types')->where('building_id', $this->building->id)->where('input_source_id', $this->master->id)->delete();
        DB::table('building_roof_types')->insert(['building_id' => $this->building->id, 'input_source_id' => $this->coach->id, 'roof_type_id' => RoofType::findByShort('flat')->id]);

        app(DwellingRows::class)->equalizeToMaster($this->building, $this->coach);

        $this->assertSame(1, $this->counts($this->coach)['building_roof_types']);
    }

    public function test_master_itself_is_never_touched(): void
    {
        app(DwellingRows::class)->equalizeToMaster($this->building, $this->master);

        $this->assertSame(['building_features' => 1, 'building_elements' => 1, 'building_roof_types' => 1], $this->counts($this->master));
    }
}
