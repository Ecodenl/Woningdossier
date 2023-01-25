<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BuildingCoachStatusControllerTest extends TestCase
{
    use WithFaker,
        RefreshDatabase;

    private array $formData;
    private Cooperation $cooperation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formData = [
            'building_coach_statuses' => [
                'coach_contact_id' => 2,
                'resident_contact_id' => 1,
            ],
        ];

        // We're not here to test client access
        /** @var Client $client */
        $client = factory(Client::class)->create();
        Sanctum::actingAs($client, ['*']);

        $this->cooperation = factory(Cooperation::class)->create();
    }

    public function test_validation()
    {
        $cooperation = $this->cooperation;

        // No data
        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')));
        $response->assertStatus(422);

        // There are no users with these contact IDs
        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')), $this->formData);
        $response->assertStatus(422);

        $resident = User::factory()->create([
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
        ]);
        $coach = User::factory()->create([
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['coach_contact_id'],
            ],
        ]);

        // There are no users (in the current cooperation) with these contact IDs
        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')), $this->formData);
        $response->assertStatus(422);

        $resident->update(['cooperation_id' => $this->cooperation->id]);
        $coach->update(['cooperation_id' => $this->cooperation->id]);

        // The users don't have the correct roles
        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')), $this->formData);
        $response->assertStatus(422);

        // Keep DB clean (if we don't, we get overlapping contact IDs)
        $resident->building->delete();
        $resident->delete();
        $coach->building->delete();
        $coach->delete();

        $resident = User::factory()->asResident()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
            'allow_access' => false,
        ]);
        $coach = User::factory()->asCoach()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['coach_contact_id'],
            ],
        ]);

        // Resident hasn't given access
        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')), $this->formData);
        $response->assertStatus(422);
        $resident->update(['allow_access' => true]);

        // Now everything should be good!
        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')), $this->formData);
        $response->assertStatus(200);

        // Coach already linked!
        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')), $this->formData);
        $response->assertStatus(422);
    }

    public function test_building_coach_status_gets_created()
    {
        $cooperation = $this->cooperation;
        // Just for this test
        config(['queue.default' => 'sync']);

        $resident = User::factory()->asResident()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
            'allow_access' => true,
        ]);
        $coach = User::factory()->asCoach()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['coach_contact_id'],
            ],
        ]);

        $response = $this->post(route('api.v1.cooperation.building-coach-status.store', compact('cooperation')), $this->formData);
        $response->assertStatus(200);

        $bcs = BuildingCoachStatus::where('coach_id', $coach->id)
            ->where('building_id', $resident->building->id)
            ->first();
        $this->assertInstanceOf(BuildingCoachStatus::class, $bcs);

        $bp = BuildingPermission::where('user_id', $coach->id)
            ->where('building_id', $resident->building->id)
            ->first();
        $this->assertInstanceOf(BuildingPermission::class, $bp);

        $pm = PrivateMessage::where('is_public', true)
            ->where('from_user_id', $coach->id)
            ->where('building_id', $resident->building->id)
            ->where('to_cooperation_id', $this->cooperation->id)
            ->first();
        $this->assertInstanceOf(PrivateMessage::class, $pm);
    }
}
