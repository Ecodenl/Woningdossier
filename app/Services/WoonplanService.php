<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Traits\FluentCaller;

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
        if ($this->isObserving) {
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

    public function userIsObserving(): self
    {
        $this->isObserving = true;

        return $this;
    }

    public function canEnterExpertScan(Cooperation $cooperation)
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
        $steps = $this->scan->steps()->where('short', '!=', 'small-measures')->get();
        foreach ($steps as $step) {
            if ($this->building->hasNotCompleted($step, $this->inputSource)) {
                return false;
            }
        }
        return true;
    }

    public function buildingHasMeasureApplications(): bool
    {
        // simple method to check whether the user has measure applications
        // in his user action plan advice.
        return $this->building->user->actionPlanAdvices()
            ->withInvisible()
            ->forInputSource($this->inputSource)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->exists();
    }
}
