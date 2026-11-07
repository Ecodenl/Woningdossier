<?php

namespace Tests\Feature\app\Listeners;

use App\Events\AccountVerified;
use App\Jobs\SmartTwin\Out\CreateCoachAccount;
use App\Jobs\SmartTwin\Out\CreateUserAccount;
use App\Listeners\SmartTwinEventSubscriber;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\User;
use Database\Seeders\InputSourcesTableSeeder;
use Database\Seeders\RoleTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * An account gets one SmartTwin user, and only the cooperations that take part in SmartTwin have a
 * say in whether it gets one and in which role.
 */
final class SmartTwinEventSubscriberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([InputSourcesTableSeeder::class, RoleTableSeeder::class]);
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        // Only what the subscriber dispatches is under test, not what the jobs then do.
        Bus::fake([CreateUserAccount::class, CreateCoachAccount::class]);
    }

    private function verify(Account $account): void
    {
        app(SmartTwinEventSubscriber::class)->handleAccountVerified(new AccountVerified($account));
    }

    public function test_a_resident_of_a_cooperation_that_takes_part_gets_a_smart_twin_user(): void
    {
        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => Cooperation::factory()->create()->id]);

        $this->verify($resident->account);

        Bus::assertDispatched(CreateUserAccount::class);
    }

    public function test_an_account_whose_cooperations_all_stay_out_gets_none(): void
    {
        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => Cooperation::factory()->create(['smarttwin_enabled' => false])->id]);

        $this->verify($resident->account);

        Bus::assertNotDispatched(CreateUserAccount::class);
        Bus::assertNotDispatched(CreateCoachAccount::class);
    }

    public function test_a_coach_role_in_a_cooperation_that_stays_out_does_not_make_the_account_an_advisor(): void
    {
        // Coach would win over resident if both counted.
        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => Cooperation::factory()->create()->id]);
        User::factory()->asCoach()->create([
            'account_id'     => $resident->account_id,
            'cooperation_id' => Cooperation::factory()->create(['smarttwin_enabled' => false])->id,
        ]);

        $this->verify($resident->account);

        Bus::assertDispatched(CreateUserAccount::class);
        Bus::assertNotDispatched(CreateCoachAccount::class);
    }

    public function test_nothing_is_dispatched_when_smart_twin_is_off_for_the_environment(): void
    {
        config()->set('hoomdossier.services.smarttwin.enabled', false);

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => Cooperation::factory()->create()->id]);

        $this->verify($resident->account);

        Bus::assertNotDispatched(CreateUserAccount::class);
    }
}
