<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\Role;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The resident's messages. With SmartTwin on, the coach tile from the dashboard sits beside the
 * conversation; with it off the page is what it has always been.
 */
final class MessagesControllerTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->cooperation = Cooperation::factory()->create();

        $resident = User::factory()
            ->withAccount()
            ->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);

        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        // The page sends a resident without a conversation elsewhere, so there has to be one.
        PrivateMessage::create([
            'building_id' => $this->building->id,
            'from_user_id' => $resident->id,
            'from_user' => $resident->getFullName(),
            'message' => 'Een eerste bericht',
            'is_public' => true,
            'to_cooperation_id' => $this->cooperation->id,
        ]);

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        $this->actingAs($resident->account);
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

    private function messagesPage(): \Illuminate\Testing\TestResponse
    {
        return $this->get(route('cooperation.my-account.messages.edit', ['cooperation' => $this->cooperation]));
    }

    public function test_the_coach_tile_sits_beside_the_conversation_when_smart_twin_is_on(): void
    {
        $this->enableSmartTwin(true);

        $coach = User::factory()->withAccount()->asCoach()->create(['cooperation_id' => $this->cooperation->id]);
        BuildingCoachStatusService::giveAccess($coach, $this->building);

        $response = $this->messagesPage();

        $response->assertOk();
        $response->assertSee(__('home.dashboard.coach.name'), false);
        // Escaped like the page escapes it: a generated name can carry an apostrophe.
        $response->assertSee($coach->getFullName());
        $response->assertSee('Een eerste bericht', false);
    }

    public function test_the_tile_does_not_offer_a_way_to_the_page_it_is_on(): void
    {
        $this->enableSmartTwin(true);

        // The dashboard's contact button leads here; repeating it here would be a link to itself.
        $this->messagesPage()->assertDontSee(__('home.dashboard.coach.contact'), false);
    }

    public function test_the_tile_keeps_its_shape_before_a_coach_is_attached(): void
    {
        $this->enableSmartTwin(true);

        $response = $this->messagesPage();

        $response->assertSee(__('home.dashboard.coach.none'), false);
        $response->assertSee(__('home.dashboard.coach.no-appointment'), false);
    }

    public function test_the_page_is_unchanged_when_smart_twin_is_off(): void
    {
        $this->enableSmartTwin(false);

        $response = $this->messagesPage();

        $response->assertOk();
        // "Energiecoach" alone could turn up elsewhere on the page; the tile always renders one of
        // these two lines, and nothing else says either.
        $response->assertDontSee(__('home.dashboard.coach.none'), false);
        $response->assertDontSee(__('home.dashboard.coach.no-appointment'), false);
        $response->assertSee('Een eerste bericht', false);
    }
}
