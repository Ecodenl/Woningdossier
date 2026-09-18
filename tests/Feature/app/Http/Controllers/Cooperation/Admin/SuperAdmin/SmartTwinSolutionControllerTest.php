<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Cooperation\Admin\SuperAdmin\SmartTwinSolutionController;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Mapping;
use App\Models\MeasureApplication;
use App\Models\Role;
use App\Models\SmartTwinSolution;
use App\Models\User;
use App\Services\MappingService;
use App\Services\SmartTwin\Mapping\Mappers\SolutionMeasures;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The screen where somebody who knows both catalogues records which measure a SmartTwin product is.
 */
final class SmartTwinSolutionControllerTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const CAVITY = 'Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls';
    private const SUN_BLINDS = 'Install Sun Blinds|SmartTwin:Screens';

    private Cooperation $ownCooperation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->ownCooperation = $this->actAsSuperAdmin();
    }

    public function test_index_lists_the_catalogue_grouped_by_kind(): void
    {
        $this->solution(self::CAVITY, 'EPS parels');
        $this->solution(self::SUN_BLINDS, 'Screens');

        $response = $this->get($this->indexRoute());

        $response->assertOk();
        $response->assertSee('EPS parels');
        $response->assertSee('Insulate Facade Cavity');
        $response->assertSee('Install Sun Blinds');
    }

    public function test_index_counts_the_products_nobody_has_decided_on(): void
    {
        $cavity = $this->solution(self::CAVITY);
        $this->solution(self::SUN_BLINDS);
        $this->couple($cavity, MeasureApplication::findByShort('cavity-wall-insulation'));

        $this->get($this->indexRoute())->assertSee('Nog 1 product zonder keuze.');
    }

    public function test_it_couples_a_product_to_a_measure(): void
    {
        $cavity = $this->solution(self::CAVITY);
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        $this->put($this->coupleRoute(), ['couplings' => [$cavity->id => (string) $measure->id]])
            ->assertRedirect($this->indexRoute());

        // Asserted through the lookup the mapping itself uses, so the two cannot drift apart.
        $this->assertSame('cavity-wall-insulation', (new SolutionMeasures())->shortFor(self::CAVITY));
    }

    public function test_a_product_deliberately_left_uncoupled_is_written_down_as_such(): void
    {
        // The difference between this and an undecided product is the whole point: sun blinds are
        // not a measure here and never will be, and that is worth recording once.
        $sunBlinds = $this->solution(self::SUN_BLINDS);

        $this->put($this->coupleRoute(), [
            'couplings' => [$sunBlinds->id => SmartTwinSolutionController::NOT_COUPLED],
        ]);

        $mapping = $this->mappingFor($sunBlinds);

        $this->assertNotNull($mapping);
        $this->assertNull($mapping->target_model_id);
        $this->assertNull((new SolutionMeasures())->shortFor(self::SUN_BLINDS));
    }

    public function test_clearing_a_choice_returns_the_product_to_undecided(): void
    {
        $cavity = $this->solution(self::CAVITY);
        $this->couple($cavity, MeasureApplication::findByShort('cavity-wall-insulation'));

        $this->put($this->coupleRoute(), ['couplings' => [$cavity->id => '']]);

        $this->assertNull($this->mappingFor($cavity));
    }

    public function test_recoupling_replaces_the_previous_measure_rather_than_adding_one(): void
    {
        $cavity = $this->solution(self::CAVITY);
        $this->couple($cavity, MeasureApplication::findByShort('cavity-wall-insulation'));
        $other = MeasureApplication::findByShort('facade-wall-insulation');

        $this->put($this->coupleRoute(), ['couplings' => [$cavity->id => (string) $other->id]]);

        $this->assertSame(1, Mapping::forType(SmartTwinSolution::mappingType())->count());
        $this->assertSame('facade-wall-insulation', (new SolutionMeasures())->shortFor(self::CAVITY));
    }

    public function test_it_ignores_a_solution_that_was_never_imported(): void
    {
        // The form is built from the catalogue, so anything else was never on the page. Writing it
        // would produce a coupling nobody can see, let alone undo, from this screen.
        $measure = MeasureApplication::findByShort('cavity-wall-insulation');

        $this->put($this->coupleRoute(), ['couplings' => [999999 => (string) $measure->id]]);

        $this->assertSame(0, Mapping::forType(SmartTwinSolution::mappingType())->count());
    }

    public function test_it_refuses_a_measure_that_does_not_exist(): void
    {
        $cavity = $this->solution(self::CAVITY);

        $this->put($this->coupleRoute(), ['couplings' => [$cavity->id => '999999']])
            ->assertSessionHasErrors("couplings.{$cavity->id}");

        $this->assertSame(0, Mapping::forType(SmartTwinSolution::mappingType())->count());
    }

    public function test_couplings_of_other_types_are_left_alone(): void
    {
        // `mappings` is shared ground, and detach() without a type goes over every row of a source
        // regardless of what it was mapping.
        $cavity = $this->solution(self::CAVITY);
        DB::table('mappings')->insert([
            'from_model_type' => $cavity->getMorphClass(),
            'from_model_id' => $cavity->id,
            'type' => 'something-else',
        ]);

        $this->put($this->coupleRoute(), ['couplings' => [$cavity->id => '']]);

        $this->assertSame(1, Mapping::where('type', 'something-else')->count());
    }

    public function test_an_id_the_catalogue_does_not_hold_resolves_to_nothing(): void
    {
        // A product SmartTwin advises before anybody imported it. Same outcome as an uncoupled one,
        // and the mapping reports the id either way — nothing quietly reaches the woonplan.
        $this->assertNull((new SolutionMeasures())->shortFor('Verzonnen|Id:Product'));
    }

    public function test_a_resident_cannot_reach_the_screen(): void
    {
        $this->actAsResident();

        $this->get($this->indexRoute())->assertForbidden();
    }

    private function indexRoute(): string
    {
        return route('cooperation.admin.super-admin.smart-twin-solutions.index', [
            'cooperation' => $this->ownCooperation,
        ]);
    }

    private function coupleRoute(): string
    {
        return route('cooperation.admin.super-admin.smart-twin-solutions.couple', [
            'cooperation' => $this->ownCooperation,
        ]);
    }

    private function solution(string $externalId, string $name = 'Product'): SmartTwinSolution
    {
        return SmartTwinSolution::create([
            'external_id' => $externalId,
            'name' => $name,
            'kind' => SmartTwinSolution::kindOf($externalId),
            'last_seen_at' => now(),
        ]);
    }

    private function couple(SmartTwinSolution $solution, MeasureApplication $measure): void
    {
        MappingService::init()
            ->from($solution)
            ->type(SmartTwinSolution::mappingType())
            ->sync([$measure], SmartTwinSolution::mappingType());
    }

    private function mappingFor(SmartTwinSolution $solution): ?Mapping
    {
        return Mapping::forType(SmartTwinSolution::mappingType())
            ->where('from_model_type', $solution->getMorphClass())
            ->where('from_model_id', $solution->id)
            ->first();
    }

    private function actAsSuperAdmin(): Cooperation
    {
        return $this->actAsRole(RoleHelper::ROLE_SUPER_ADMIN);
    }

    private function actAsResident(): Cooperation
    {
        return $this->actAsRole(RoleHelper::ROLE_RESIDENT);
    }

    private function actAsRole(string $role): Cooperation
    {
        $cooperation = Cooperation::factory()->create();
        $account = Account::factory()->create();
        $user = User::factory()->create([
            'account_id' => $account->id,
            'cooperation_id' => $cooperation->id,
        ]);
        $user->assignRole($role);
        $building = Building::factory()->create(['user_id' => $user->id]);

        $this->actingAs($account);
        HoomdossierSession::setHoomdossierSessions(
            $building,
            InputSource::master(),
            InputSource::master(),
            Role::findByName($role),
        );
        HoomdossierSession::setCooperation($cooperation);

        return $cooperation;
    }
}
