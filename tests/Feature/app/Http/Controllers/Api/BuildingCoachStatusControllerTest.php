<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Models\Building;
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

final class BuildingCoachStatusControllerTest extends TestCase
{
    use WithFaker,
        RefreshDatabase;

    private array $formData;
    private Cooperation $cooperation;
    public bool $seed = true;

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
        $client = Client::factory()->create();
        Sanctum::actingAs($client, ['*']);

        $this->cooperation = Cooperation::factory()->create();
    }

    public function testValidation(): void
    {
        $cooperation = $this->cooperation;

        // No data
        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation'))
        );
        $response->assertStatus(422);

        // There are no users with these contact IDs
        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation')),
            $this->formData
        );
        $response->assertStatus(422);

        $resident = User::factory()->withAccount()->create([
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
        ]);
        Building::factory()->create(['user_id' => $resident->id]);

        $coach = User::factory()->withAccount()->create([
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['coach_contact_id'],
            ],
        ]);
        Building::factory()->create(['user_id' => $coach->id]);

        // There are no users (in the current cooperation) with these contact IDs
        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation')),
            $this->formData
        );
        $response->assertStatus(422);

        $resident->update(['cooperation_id' => $this->cooperation->id]);
        $coach->update(['cooperation_id' => $this->cooperation->id]);

        // The users don't have the correct roles
        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation')),
            $this->formData
        );
        $response->assertStatus(422);

        // Keep DB clean (if we don't, we get overlapping contact IDs)
        $resident->building->delete();
        $resident->delete();
        $coach->building->delete();
        $coach->delete();

        $resident = User::factory()->withAccount()->asResident()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
            'allow_access' => false,
        ]);
        Building::factory()->create(['user_id' => $resident->id]);

        $coach = User::factory()->withAccount()->asCoach()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['coach_contact_id'],
            ],
        ]);
        Building::factory()->create(['user_id' => $coach->id]);

        // Resident hasn't given access
        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation')),
            $this->formData
        );
        $response->assertStatus(422);
        $resident->update(['allow_access' => true]);

        // Now everything should be good!
        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation')),
            $this->formData
        );
        $response->assertStatus(200);

        // Coach already linked!
        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation')),
            $this->formData
        );
        $response->assertStatus(422);
    }

    public function testBuildingCoachStatusGetsCreated(): void
    {
        $cooperation = $this->cooperation;
        // Just for this test // IS ALREADY THE DEFAULT
        //config(['queue.default' => 'sync']);

        $resident = User::factory()->withAccount()->asResident()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
            'allow_access' => true,
        ]);
        Building::factory()->create(['user_id' => $resident->id]);

        $coach = User::factory()->withAccount()->asCoach()->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['coach_contact_id'],
            ],
        ]);
        Building::factory()->create(['user_id' => $coach->id]);

        $response = $this->post(
            route('api.v1.cooperation.building-coach-status.store', compact('cooperation')),
            $this->formData
        );
        $response->assertStatus(200);

        $this->assertDatabaseHas('building_coach_statuses', [
            'coach_id' => $coach->id,
            'building_id' => $resident->building->id,
        ]);

        $this->assertDatabaseHas('building_permissions', [
            'user_id' => $coach->id,
            'building_id' => $resident->building->id,
        ]);

        $this->assertDatabaseHas('private_messages', [
            'is_public' => true,
            'from_user_id' => $coach->id,
            'building_id' => $resident->building->id,
            'to_cooperation_id' => $this->cooperation->id,
        ]);
    }
}
