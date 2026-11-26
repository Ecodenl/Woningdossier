<?php

namespace App\Services;

use App\Enums\AdviceSource;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\Step;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Builder;

class WoonplanService
{
    use FluentCaller;

    public bool $isObserving = false;
    public Scan $scan;
    public Building $building;
    public InputSource $inputSource;

    public function __construct(Building $building)
    {
        $this->building    = $building;
        $this->inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function scan(Scan $scan): self
    {
        $this->scan = $scan;
        return $this;
    }

    public function canAccessWoonplan(): bool
    {
        // when a user is observing another building, he can always see the Woonplan
        if ($this->isObserving || static::guardIsSkipped()) {
            return true;
        }
        // if the user is on the quick scan some abnormal rules apply
        // the user is allowed to access the woonnplan when he has measure applications or the first four steps are completed
        // this is done so when we incomplete steps due to a upgrade, existing users can still acces their woonplan
        if ($this->scan->isQuickScan()) {
            return $this->buildingCompletedFirstFourSteps() || $this->buildingHasMeasureApplications();
        }


        return $this->building->hasCompletedScan($this->scan, $this->inputSource);
    }

    /**
     * Escape hatch for local development and the test environment, so a tester can reach the
     * woonplan (and everything on it, such as the SmartTwin hand-off) without first filling in
     * the scan. Never applies on production.
     */
    public static function guardIsSkipped(): bool
    {
        if (app()->isLocal()) {
            return true;
        }

        return config('hoomdossier.skip_woonplan_guard', false) && ! app()->environment('production');
    }

    public function userIsObserving(): self
    {
        $this->isObserving = true;

        return $this;
    }

    public function canEnterExpertScan(Cooperation $cooperation): bool
    {
        // first check that the cooperation has the expert-scan
        if ($cooperation->scans()->where('short', Scan::EXPERT)->exists()) {
            // basically the same check that we use for the access on woonplan
            if ($this->buildingCompletedFirstFourSteps() || $this->buildingHasMeasureApplications()) {
                return true;
            }
        }
        return false;
    }

    public function buildingCompletedFirstFourSteps(): bool
    {
        /** @var \Illuminate\Support\Collection<\App\Models\Step> $steps */
        $steps = $this->scan->steps()->where('short', '!=', 'small-measures')->get();
        foreach ($steps as $step) {
            if ($this->building->hasNotCompleted($step, $this->inputSource)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Whether there is a plan to put on the woonplan.
     *
     * The board is built from the user's action plan advices, whatever advised them, so with none of
     * them there are three empty columns and nothing else. This is a different question from
     * canAccessWoonplan(): that one asks how far the scan got, which says nothing about whether the
     * advice is in -- it is "far enough" for a building that filled the scan in before SmartTwin was
     * switched on, and always "far enough" where the guard is skipped.
     *
     * What counts is an advice of SmartTwin, or a calculated measure from before SmartTwin. What comes
     * from the resident's own answers does not, as those can be in before the check:
     * - the small measures, which the quick scan calculates from its own questions, also for a
     *   building SmartTwin has yet to advise;
     * - the cooperation's measures and own measures ticked under Woonwensen ("Welke zaken
     *   vervangen"), which go into the action plan as soon as they are ticked.
     * All of them are still there after SmartTwin's first advice, and show on the woonplan then.
     *
     * Trashed advices count. They are not on the board, but the resident can put them back from it,
     * which they cannot do from the invitation screen.
     */
    public function hasPlan(): bool
    {
        $smallMeasures = Step::findByShort('small-measures');

        return $this->building->user->userActionPlanAdvices()
            ->withInvisible()
            ->forInputSource($this->inputSource)
            ->where(fn (Builder $query) => $query
                // Whatever it was mapped onto, including a small measure coupled to a solution.
                ->where('source', AdviceSource::SMART_TWIN)
                ->orWhere(fn (Builder $query) => $query
                    ->where('user_action_plan_advisable_type', MeasureApplication::class)
                    ->where(fn (Builder $query) => $query
                        ->whereNull('step_id')
                        ->orWhere('step_id', '!=', $smallMeasures->id))))
            ->exists();
    }

    public function buildingHasMeasureApplications(): bool
    {
        // simple method to check whether the user has measure applications
        // in his user action plan advice.
        return $this->building->user->userActionPlanAdvices()
            ->withInvisible()
            ->forInputSource($this->inputSource)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->exists();
    }
}
