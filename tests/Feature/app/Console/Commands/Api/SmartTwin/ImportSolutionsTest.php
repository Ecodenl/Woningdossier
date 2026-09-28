<?php

namespace Tests\Feature\app\Console\Commands\Api\SmartTwin;

use App\Models\SmartTwinSolution;
use App\Services\SmartTwin\Api\Resources\Advice;
use App\Services\SmartTwin\Api\SmartTwinApi;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

/**
 * Importing the catalogue. Coupling is not this command's business — that is the screen's, and a
 * command deciding it too would undo their work on every run.
 */
final class ImportSolutionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This command needs nothing seeded, but the suite does.
     *
     * RefreshDatabase migrates once per process and only the first test class to run decides
     * whether that migration seeds. This file sorts first of all feature tests, so leaving it
     * unseeded leaves every later test without roles, elements or measures.
     */
    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const CAVITY = 'Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls';
    private const SUN_BLINDS = 'Install Sun Blinds|SmartTwin:Screens';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);
    }

    public function test_it_imports_the_catalogue(): void
    {
        $this->catalogue([
            ['id' => self::CAVITY, 'name' => 'EPS parels'],
            ['id' => self::SUN_BLINDS, 'name' => 'Screens'],
        ]);

        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        $this->assertSame(2, SmartTwinSolution::count());
        $this->assertSame('Insulate Facade Cavity', SmartTwinSolution::firstWhere('external_id', self::CAVITY)->kind);
    }

    public function test_a_second_run_updates_rather_than_duplicates(): void
    {
        $this->catalogue([['id' => self::CAVITY, 'name' => 'EPS parels']]);
        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        $this->catalogue([['id' => self::CAVITY, 'name' => 'EPS parels (nieuw)']]);
        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        $this->assertSame(1, SmartTwinSolution::count());
        $this->assertSame('EPS parels (nieuw)', SmartTwinSolution::firstWhere('external_id', self::CAVITY)->name);
    }

    public function test_a_withdrawn_product_is_kept_and_marked(): void
    {
        // Deleting it would take its coupling with it, and an advice from before the withdrawal
        // still names the product.
        $this->catalogue([
            ['id' => self::CAVITY, 'name' => 'EPS parels'],
            ['id' => self::SUN_BLINDS, 'name' => 'Screens'],
        ]);
        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        // The two imports have to land on different timestamps for "not seen last time" to mean
        // anything; a catalogue does not change twice within a second in practice.
        Carbon::setTestNow(Carbon::now()->addMinute());

        $this->catalogue([['id' => self::CAVITY, 'name' => 'EPS parels']]);
        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        $this->assertSame(2, SmartTwinSolution::count());
        $this->assertSame([self::SUN_BLINDS], SmartTwinSolution::withdrawn()->pluck('external_id')->all());
    }

    public function test_an_empty_catalogue_changes_nothing(): void
    {
        // Far more often a failing call than a withdrawn catalogue, and acting on it would mark
        // every single product as gone.
        $this->catalogue([['id' => self::CAVITY, 'name' => 'EPS parels']]);
        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        $this->catalogue([]);
        $this->artisan('api:smarttwin:import-solutions')->assertFailed();

        $this->assertSame(1, SmartTwinSolution::count());
    }

    public function test_a_dry_run_writes_nothing(): void
    {
        $this->catalogue([['id' => self::CAVITY, 'name' => 'EPS parels']]);

        $this->artisan('api:smarttwin:import-solutions', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame(0, SmartTwinSolution::count());
    }

    public function test_it_refuses_to_run_with_the_integration_switched_off(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $this->artisan('api:smarttwin:import-solutions')->assertFailed();
    }

    public function test_a_solution_without_an_id_is_left_out(): void
    {
        $this->catalogue([
            ['id' => self::CAVITY, 'name' => 'EPS parels'],
            ['name' => 'Naamloos product'],
        ]);

        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        $this->assertSame(1, SmartTwinSolution::count());
    }

    public function test_an_id_without_a_kind_still_imports(): void
    {
        // The `kind|provider:product` shape is undocumented, so it is read where it helps and the
        // product imports either way — its coupling hangs off the full id.
        $this->catalogue([['id' => 'LosseWaarde', 'name' => 'Los product']]);

        $this->artisan('api:smarttwin:import-solutions')->assertSuccessful();

        $this->assertNull(SmartTwinSolution::firstWhere('external_id', 'LosseWaarde')->kind);
    }

    /**
     * @param  array<int, array<string, mixed>>  $solutions
     */
    private function catalogue(array $solutions): void
    {
        $advice = Mockery::mock(Advice::class);
        $advice->shouldReceive('getAllSolutions')->andReturn(['solutions' => $solutions]);
        // The command logs which endpoint it read, so it asks the resource for its uri.
        $advice->shouldReceive('uri')->andReturn('api/advice/v1/solutions');

        $api = Mockery::mock(SmartTwinApi::class);
        $api->shouldReceive('advice')->andReturn($advice);

        $this->instance(SmartTwinApi::class, $api);
    }
}
