<?php

namespace Tests\Feature\app\Listeners;

use App\Events\SmartTwinDisabledForCooperation;
use App\Jobs\SmartTwin\UndoSmartTwinForBuilding;
use App\Listeners\UndoSmartTwinForCooperation;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class UndoSmartTwinForCooperationTest extends TestCase
{
    use RefreshDatabase;

    private function buildingIn(Cooperation $cooperation): Building
    {
        $user = User::factory()->withAccount()->create(['cooperation_id' => $cooperation->id]);

        return Building::factory()->create(['user_id' => $user->id]);
    }

    public function test_it_listens_to_a_cooperation_being_switched_off(): void
    {
        Event::fake();

        Event::assertListening(SmartTwinDisabledForCooperation::class, UndoSmartTwinForCooperation::class);
    }

    public function test_every_building_of_the_cooperation_is_taken_back_and_no_other(): void
    {
        Bus::fake([UndoSmartTwinForBuilding::class]);

        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false]);
        $first = $this->buildingIn($cooperation);
        $second = $this->buildingIn($cooperation);
        $elsewhere = $this->buildingIn(Cooperation::factory()->create());

        (new UndoSmartTwinForCooperation())->handle(new SmartTwinDisabledForCooperation($cooperation));

        Bus::assertDispatchedTimes(UndoSmartTwinForBuilding::class, 2);
        Bus::assertDispatched(UndoSmartTwinForBuilding::class, fn ($job) => $job->building->is($first));
        Bus::assertDispatched(UndoSmartTwinForBuilding::class, fn ($job) => $job->building->is($second));
        Bus::assertNotDispatched(UndoSmartTwinForBuilding::class, fn ($job) => $job->building->is($elsewhere));
    }
}
