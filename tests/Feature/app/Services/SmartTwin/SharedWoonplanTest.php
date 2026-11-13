<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\SharedWoonplan;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * After the switch the woonplan is the same for resident, coach and master: whatever an action
 * wrote to master, the other two get as well.
 */
final class SharedWoonplanTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = User::first()->building;
    }

    private function smallMeasure(string $short, InputSource $inputSource, array $attributes = []): UserActionPlanAdvice
    {
        $measure = MeasureApplication::findByShort($short);

        return UserActionPlanAdvice::withoutGlobalScopes()->create(array_merge([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => $inputSource->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => $measure->id,
            'step_id'                         => Step::findByShort('small-measures')->id,
            'costs'                           => ['from' => 10, 'to' => 45],
            'category'                        => 'to-do',
            'visible'                         => true,
        ], $attributes));
    }

    /**
     * @return Collection<string, UserActionPlanAdvice> By input source short.
     */
    private function rowsOf(string $advisableType, ?int $advisableId = null): Collection
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_type', $advisableType)
            ->when(! is_null($advisableId), fn ($q) => $q->where('user_action_plan_advisable_id', $advisableId))
            ->get()
            ->keyBy(fn (UserActionPlanAdvice $row) => InputSource::find($row->input_source_id)->short);
    }

    public function test_the_resident_and_the_coach_get_what_master_has(): void
    {
        // Moved to "later" by the resident: their row and master, not the coach.
        $advice = $this->smallMeasure('save-energy-with-light', InputSource::resident());
        $advice->update(['category' => 'later', 'order' => 3]);

        app(SharedWoonplan::class)->copyFromMaster($advice);

        $rows = $this->rowsOf(MeasureApplication::class, $advice->user_action_plan_advisable_id);
        $this->assertEqualsCanonicalizing(['resident', 'coach', 'master'], $rows->keys()->all());
        foreach ($rows as $row) {
            $this->assertSame('later', $row->category);
            $this->assertSame(3, (int) $row->order);
            // JSON columns are copied as stored, not encoded a second time.
            $this->assertEquals(['from' => 10, 'to' => 45], $row->costs);
        }
    }

    public function test_an_own_measure_gets_a_sibling_in_each_input_source(): void
    {
        $own = CustomMeasureApplication::factory()->create([
            'building_id'     => $this->building->id,
            'input_source_id' => InputSource::resident()->id,
        ]);
        $advice = UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id'                         => $this->building->user_id,
            'input_source_id'                 => InputSource::resident()->id,
            'user_action_plan_advisable_type' => CustomMeasureApplication::class,
            'user_action_plan_advisable_id'   => $own->id,
            'costs'                           => ['from' => null, 'to' => 500],
            'category'                        => 'to-do',
            'visible'                         => true,
        ]);

        app(SharedWoonplan::class)->copyFromMaster($advice);

        $coachSibling = $own->getSibling(InputSource::coach());
        $this->assertInstanceOf(CustomMeasureApplication::class, $coachSibling);
        $this->assertTrue(UserActionPlanAdvice::withoutGlobalScopes()
            ->where('input_source_id', InputSource::coach()->id)
            ->where('user_action_plan_advisable_type', CustomMeasureApplication::class)
            ->where('user_action_plan_advisable_id', $coachSibling->id)
            ->exists());
    }

    public function test_equalizing_removes_what_master_no_longer_has(): void
    {
        // The coach's small measure from their own answers, which a later recalculation of the
        // resident removed from master.
        $this->smallMeasure('save-energy-with-light', InputSource::coach());
        UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('input_source_id', InputSource::master()->id)
            ->delete();
        $this->smallMeasure('improve-radiators', InputSource::resident());

        app(SharedWoonplan::class)->equalizeToMaster($this->building, Step::findByShort('small-measures'));

        $this->assertCount(0, $this->rowsOf(MeasureApplication::class, MeasureApplication::findByShort('save-energy-with-light')->id));
        $this->assertCount(3, $this->rowsOf(MeasureApplication::class, MeasureApplication::findByShort('improve-radiators')->id));
    }
}
