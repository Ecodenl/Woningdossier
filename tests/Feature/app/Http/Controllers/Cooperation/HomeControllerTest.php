<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\Scan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The route behind "/home" is two pages depending on whether SmartTwin drives the scan: the start
 * screen it has always been, or the dashboard. These check that each mode gets its own, since a
 * mistake here is invisible until someone opens the page in the mode they were not testing.
 */
final class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private User $resident;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        // These render a full page, and the asset manifest is a build artefact that CI does not
        // produce. What is under test is the markup, not which bundle it links to.
        $this->withoutVite();

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $this->resident = User::factory()
            ->withAccount()
            ->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);

        $this->building = Building::factory()->create(['user_id' => $this->resident->id]);

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        $this->actingAs($this->resident->account);
        HoomdossierSession::setCooperation($this->cooperation);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );
    }

    private function enableSmartTwin(bool $enabled): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', $enabled);
    }

    public function test_start_screen_is_shown_when_smart_twin_is_disabled(): void
    {
        $this->enableSmartTwin(false);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertDontSee(__('home.dashboard.building.title'), false);
        $response->assertDontSee(__('home.dashboard.coach.title'), false);
        $response->assertDontSee(__('home.dashboard.files.title'), false);
    }

    public function test_dashboard_is_shown_when_smart_twin_is_enabled(): void
    {
        $this->enableSmartTwin(true);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertSee(__('home.dashboard.building.title'), false);
        $response->assertSee(__('home.dashboard.coach.title'), false);
        $response->assertSee(__('home.dashboard.files.title'), false);
        $response->assertSee(__('home.dashboard.building.to-dossier'), false);
    }

    public function test_dashboard_shows_what_is_known_about_the_building(): void
    {
        $this->enableSmartTwin(true);

        // The building factory leaves the features to whatever fills them in, so there is nothing to
        // update here yet. The dashboard reads the master row.
        BuildingFeature::create([
            'building_id' => $this->building->id,
            'input_source_id' => InputSource::findByShort(InputSource::MASTER_SHORT)->id,
            'build_year' => 2015,
            'surface' => 139,
            'energy_label_id' => EnergyLabel::where('name', 'C')->first()->id,
        ]);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertSee('2015', false);
        $response->assertSee('139 m', false);
        // The official label renders as the coloured arrow rather than the letter.
        $response->assertSee('icon-label-c', false);
    }

    public function test_dashboard_says_so_when_nothing_is_known_yet(): void
    {
        $this->enableSmartTwin(true);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        // The estimated energy label has no source yet, so this row is always unknown for now.
        $response->assertSee(__('home.dashboard.building.unknown'), false);
        $response->assertSee(__('home.dashboard.coach.none'), false);
        $response->assertSee(__('home.dashboard.coach.no-appointment'), false);
    }
}
