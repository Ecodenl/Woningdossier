<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Helpers\ScanAvailabilityHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\BuildingTypeCategory;
use App\Models\Cooperation;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\User;
use App\Services\Scans\ScanFlowService;
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

    public function test_start_screen_is_shown_when_the_cooperation_does_not_take_part(): void
    {
        // SmartTwin is on for the environment, but not for this cooperation.
        $this->enableSmartTwin(true);
        $this->cooperation->update(['smarttwin_enabled' => false]);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertDontSee(__('home.dashboard.building.title'), false);
        $response->assertDontSee(__('home.dashboard.coach.title'), false);
    }

    public function test_logo_takes_a_resident_to_the_dashboard(): void
    {
        $this->enableSmartTwin(true);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertSee($this->logoLink(route('cooperation.home', ['cooperation' => $this->cooperation])), false);
    }

    public function test_logo_takes_a_management_role_to_the_admin_index(): void
    {
        $this->enableSmartTwin(true);
        $this->resident->assignRole(RoleHelper::ROLE_COACH);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        // The admin index picks between the role's start page and the role picker itself.
        $response->assertSee($this->logoLink(route('cooperation.admin.index', ['cooperation' => $this->cooperation])), false);
    }

    public function test_woningdossier_in_the_nav_opens_a_scan_the_building_has(): void
    {
        $this->enableSmartTwin(true);

        // The cooperation offers only the lite scan, but this building was switched to the quick
        // scan. The navigation used to take the cooperation's first scan regardless of the building,
        // and landed on a woonplan whose sub navigation had none of the steps.
        $liteScan = Scan::findByShort(Scan::LITE);
        $quickScan = Scan::findByShort(Scan::QUICK);
        $this->cooperation->scans()->sync([$liteScan->id]);
        ScanAvailabilityHelper::setAvailability($this->building, $quickScan, true);
        ScanAvailabilityHelper::setAvailability($this->building, $liteScan, false);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $this->assertMatchesRegularExpression($this->navLink($this->dossierUrl($quickScan)), $response->getContent());
    }

    public function test_dashboard_offers_each_scan_the_building_has(): void
    {
        $this->enableSmartTwin(true);
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::LITE)->id);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        foreach ([Scan::QUICK, Scan::LITE] as $short) {
            $scan = Scan::findByShort($short);

            $response->assertSee('href="' . $this->dossierUrl($scan) . '"', false);
            $response->assertSee(__('home.start.buttons.start', ['scan' => $scan->name]), false);
        }
        $response->assertDontSee(__('home.dashboard.building.to-dossier'), false);
    }

    public function test_woningdossier_in_the_nav_follows_the_scan_the_user_went_into(): void
    {
        $this->enableSmartTwin(true);
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::LITE)->id);

        foreach ([Scan::LITE, Scan::QUICK] as $short) {
            $scan = Scan::findByShort($short);

            $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.index', [
                'cooperation' => $this->cooperation,
                'scan' => $scan,
            ]))->assertOk();

            $response = $this->get(route('cooperation.frontend.help.index', ['cooperation' => $this->cooperation]));

            $response->assertOk();
            $this->assertMatchesRegularExpression($this->navLink($this->dossierUrl($scan)), $response->getContent());
        }
    }

    public function test_woningdossier_stays_out_of_the_nav_on_the_dashboard_when_there_is_a_scan_to_pick(): void
    {
        $this->enableSmartTwin(true);
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::LITE)->id);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $this->assertDoesNotMatchRegularExpression($this->woningdossierNavItem(), $response->getContent());

        // Not even once a scan has been picked: on the dashboard, its buttons are the way in.
        $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.index', [
            'cooperation' => $this->cooperation,
            'scan' => Scan::findByShort(Scan::LITE),
        ]))->assertOk();

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $this->assertDoesNotMatchRegularExpression($this->woningdossierNavItem(), $response->getContent());
    }

    public function test_woningdossier_stays_in_the_nav_on_the_dashboard_with_a_single_scan(): void
    {
        $this->enableSmartTwin(true);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $this->assertMatchesRegularExpression($this->woningdossierNavItem(), $response->getContent());
    }

    public function test_woningdossier_waits_for_a_scan_to_be_picked_on_pages_without_one(): void
    {
        $this->enableSmartTwin(true);
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::LITE)->id);

        $response = $this->get(route('cooperation.frontend.help.index', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $this->assertDoesNotMatchRegularExpression($this->woningdossierNavItem(), $response->getContent());
    }

    public function test_opening_the_files_does_not_pick_a_scan(): void
    {
        $this->enableSmartTwin(true);
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::LITE)->id);

        // The files belong to the building; their route only goes through a scan.
        $this->get(route('cooperation.frontend.tool.simple-scan.my-plan.media', [
            'cooperation' => $this->cooperation,
            'scan' => Scan::findByShort(Scan::QUICK),
        ]))->assertOk();

        $response = $this->get(route('cooperation.frontend.help.index', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $this->assertDoesNotMatchRegularExpression($this->woningdossierNavItem(), $response->getContent());
    }

    public function test_woningdossier_stays_in_the_nav_for_someone_filling_in_for_another_building(): void
    {
        $this->enableSmartTwin(true);
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::LITE)->id);

        // withBuilding(): isFillingToolForOtherBuilding() compares against the user's own building.
        $coach = User::factory()
            ->withAccount()
            ->withBuilding()
            ->asCoach()
            ->create(['cooperation_id' => $this->cooperation->id]);

        $inputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        $this->actingAs($coach->account);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName(RoleHelper::ROLE_COACH),
        );

        // A coach never gets the dashboard to pick on, so the nav is their way into the dossier.
        $response = $this->get(route('cooperation.frontend.help.index', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $this->assertMatchesRegularExpression($this->woningdossierNavItem(), $response->getContent());
    }

    private function dossierUrl(Scan $scan): string
    {
        return ScanFlowService::init($scan, $this->building, InputSource::findByShort(InputSource::MASTER_SHORT))
            ->resolveInitialUrl();
    }

    /**
     * The dashboard tile links to the dossier too, so the nav's own markup tells the two apart.
     */
    private function navLink(string $url): string
    {
        return '/<a href="' . preg_quote($url, '/') . '"\s+class="nav-item/';
    }

    /**
     * The nav's own dossier item, wherever it points.
     */
    private function woningdossierNavItem(): string
    {
        return '/class="nav-item[^"]*">\s*' . preg_quote(__('cooperation/frontend/layouts.nav.woningdossier'), '/') . '\s*<\/a>/';
    }

    /**
     * The nav below the logo links to the same pages, so only the logo's own markup tells them apart.
     */
    private function logoLink(string $url): string
    {
        return '<a href="' . $url . '" class="flex items-center">';
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

    public function test_the_estimated_label_is_smart_twins_label_of_the_dwelling_as_it_is(): void
    {
        $this->enableSmartTwin(true);

        // The current situation is C, the package would make it A+++: the dashboard is about the
        // dwelling, so C.
        SmartTwinPackage::create([
            'building_id' => $this->building->id,
            'measure_application_ids' => [],
            'current_result' => ['energyLabel' => 'C'],
            'scenario_result' => ['energyLabel' => 'A+++'],
            'imported_at' => now(),
        ]);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertSee('icon-label-c', false);
        $response->assertDontSee('A+++', false);
    }

    public function test_a_label_above_a_shows_the_arrow_of_a_with_its_name(): void
    {
        $this->enableSmartTwin(true);

        BuildingFeature::create([
            'building_id' => $this->building->id,
            'input_source_id' => InputSource::findByShort(InputSource::MASTER_SHORT)->id,
            'energy_label_id' => EnergyLabel::where('name', 'A++')->first()->id,
        ]);
        SmartTwinPackage::create([
            'building_id' => $this->building->id,
            'measure_application_ids' => [],
            'current_result' => ['energyLabel' => 'A+++'],
            'imported_at' => now(),
        ]);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertSee('icon-label-a', false);
        $response->assertSee('A++', false);
        $response->assertSee('A+++', false);
    }

    public function test_an_apartment_of_unknown_kind_shows_its_category(): void
    {
        $this->enableSmartTwin(true);

        BuildingFeature::create([
            'building_id' => $this->building->id,
            'input_source_id' => InputSource::findByShort(InputSource::MASTER_SHORT)->id,
            'building_type_category_id' => BuildingTypeCategory::where('short', 'apartment')->first()->id,
        ]);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        $response->assertSee('Appartement', false);
    }

    public function test_dashboard_says_so_when_nothing_is_known_yet(): void
    {
        $this->enableSmartTwin(true);

        $response = $this->get(route('cooperation.home', ['cooperation' => $this->cooperation]));

        $response->assertOk();
        // Nothing is known, the estimated energy label included: there is no SmartTwin advice yet.
        $response->assertSee(__('home.dashboard.building.unknown'), false);
        $response->assertDontSee('icon-label-', false);
        $response->assertSee(__('home.dashboard.coach.none'), false);
        $response->assertSee(__('home.dashboard.coach.no-appointment'), false);
    }
}
