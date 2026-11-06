<?php

namespace Tests\Feature\app\Helpers;

use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

final class StepHelperTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Building $building;
    private Step $step;

    protected function setUp(): void
    {
        parent::setUp();

        // "Mijn vragen" is only shown when SmartTwin drives the scan.
        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $cooperation = Cooperation::factory()->create();
        $cooperation->scans()->attach(Scan::quick()->id);

        $user = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $user->id]);

        $this->step = Step::findByShort('living-requirements');
    }

    public function test_a_step_is_done_without_its_optional_sub_step(): void
    {
        $subSteps = $this->step->subSteps()->withoutOptional()->get();
        $this->assertLessThan($this->step->subSteps()->count(), $subSteps->count());

        $this->completeOnMaster($subSteps);

        $this->assertTrue($this->completeStepIfNeeded());
        $this->assertTrue($this->building->hasCompleted($this->step, InputSource::resident()));
    }

    public function test_a_step_is_not_done_while_a_sub_step_that_is_not_optional_is_open(): void
    {
        $lastSubStep = $this->step->subSteps()->orderByDesc('order')->first();

        $this->completeOnMaster($this->step->subSteps()->where('id', '!=', $lastSubStep->id)->get());

        $this->assertFalse($this->completeStepIfNeeded());
        $this->assertFalse($this->building->hasCompleted($this->step, InputSource::resident()));
    }

    /**
     * Whether a step is done is decided on master, whoever filled the sub steps in.
     *
     * @param Collection<int, SubStep> $subSteps
     */
    private function completeOnMaster(Collection $subSteps): void
    {
        foreach ($subSteps as $subStep) {
            CompletedSubStep::allInputSources()->create([
                'sub_step_id' => $subStep->id,
                'building_id' => $this->building->id,
                'input_source_id' => InputSource::master()->id,
            ]);
        }
    }

    private function completeStepIfNeeded(): bool
    {
        return StepHelper::completeStepIfNeeded(
            $this->step,
            $this->building,
            InputSource::resident(),
            $this->building->user,
        );
    }
}
