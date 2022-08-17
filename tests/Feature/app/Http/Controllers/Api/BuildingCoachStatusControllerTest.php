<?php

namespace Tests\Feature\app\Http\Controllers\Api;

use App\Models\Client;
use App\Models\Cooperation;
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

        $resident = factory(User::class)->create([
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
        ]);
        $coach = factory(User::class)->create([
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

        $resident = factory(User::class)->state('asResident')->create([
            'cooperation_id' => $this->cooperation->id,
            'extra' => [
                'contact_id' => $this->formData['building_coach_statuses']['resident_contact_id'],
            ],
            'allow_access' => false,
        ]);
        $coach = factory(User::class)->state('asCoach')->create([
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
}
