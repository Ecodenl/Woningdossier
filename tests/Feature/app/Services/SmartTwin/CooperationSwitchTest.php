<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Events\SmartTwinDisabledForCooperation;
use App\Events\SmartTwinEnabledForCooperation;
use App\Helpers\Cache\Cooperation as CooperationCache;
use App\Livewire\Cooperation\Admin\SuperAdmin\Cooperations\Form;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use App\Services\SmartTwin\CooperationSwitch;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

final class CooperationSwitchTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('audit.enabled', true);
        Event::fake([SmartTwinEnabledForCooperation::class, SmartTwinDisabledForCooperation::class]);
    }

    public function test_switching_off_is_audited_and_announced(): void
    {
        $account = Account::factory()->create();
        $this->actingAs($account);
        $cooperation = Cooperation::factory()->create();

        CooperationSwitch::set($cooperation, false);

        $this->assertFalse($cooperation->fresh()->smarttwin_enabled);
        Event::assertDispatched(
            SmartTwinDisabledForCooperation::class,
            fn (SmartTwinDisabledForCooperation $event) => $event->cooperation->is($cooperation),
        );

        $audit = Audit::where('event', 'smarttwin-disabled')->sole();
        $this->assertSame($cooperation->getMorphClass(), $audit->auditable_type);
        $this->assertEquals($cooperation->id, $audit->auditable_id);
        $this->assertEquals($account->id, $audit->user_id);
        $this->assertSame(['smarttwin_enabled' => true], $audit->old_values);
        $this->assertSame(['smarttwin_enabled' => false], $audit->new_values);
    }

    public function test_switching_on_is_announced(): void
    {
        $cooperation = Cooperation::factory()->create(['smarttwin_enabled' => false]);

        CooperationSwitch::set($cooperation, true);

        $this->assertTrue($cooperation->fresh()->smarttwin_enabled);
        Event::assertDispatched(SmartTwinEnabledForCooperation::class);
        $this->assertSame(1, Audit::where('event', 'smarttwin-enabled')->count());
    }

    public function test_setting_the_value_it_already_has_does_nothing(): void
    {
        $cooperation = Cooperation::factory()->create();

        CooperationSwitch::set($cooperation, true);

        Event::assertNotDispatched(SmartTwinEnabledForCooperation::class);
        $this->assertSame(0, Audit::where('event', 'like', 'smarttwin-%')->count());
    }

    public function test_the_cached_copy_of_the_cooperation_follows_the_switch(): void
    {
        $cooperation = Cooperation::factory()->create();
        // The session hands this cached copy out, to every view among others.
        $this->assertTrue(CooperationCache::find($cooperation->id)->smarttwin_enabled);

        CooperationSwitch::set($cooperation, false);

        $this->assertFalse(CooperationCache::find($cooperation->id)->smarttwin_enabled);
    }

    public function test_a_super_admin_switches_it_from_the_cooperation_form(): void
    {
        $superAdmin = User::factory()->withAccount()->create(['cooperation_id' => Cooperation::factory()->create()->id]);
        $superAdmin->assignRole(RoleHelper::ROLE_SUPER_ADMIN);
        $this->actingAs($superAdmin->account);
        HoomdossierSession::setCooperation($superAdmin->cooperation);
        HoomdossierSession::setRole(Role::findByName(RoleHelper::ROLE_SUPER_ADMIN));

        // Read back: the factory leaves the country as an enum on the model it returns.
        $cooperation = Cooperation::factory()->create()->fresh();

        Livewire::test(Form::class, ['cooperationToEdit' => $cooperation])
            ->assertSet('smartTwinEnabled', true)
            ->set('smartTwinEnabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse($cooperation->fresh()->smarttwin_enabled);
        Event::assertDispatched(SmartTwinDisabledForCooperation::class);
    }
}
