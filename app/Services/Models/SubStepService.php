<?php

namespace App\Services\Models;

use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Traits\FluentCaller;

class SubStepService
{
    use FluentCaller;

    protected SubStep $subStep;
    protected InputSource $inputSource;
    protected InputSource $masterInputSource;
    protected Building $building;

    public function __construct()
    {
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function subStep(SubStep $subStep): self
    {
        $this->subStep = $subStep;
        return $this;
    }

    public function inputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    public function building(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    /**
     * Complete a sub step for a building.
     */
    public function complete(): CompletedSubStep
    {
        $completedSubStep = CompletedSubStep::allInputSources()->firstOrCreate([
            'sub_step_id' => $this->subStep->id,
            'input_source_id' => $this->inputSource->id,
            'building_id' => $this->building->id,
        ]);

        // If it wasn't recently created, we want to ensure the master exists, because if it isn't being created
        // then it won't save to the master either. By hitting a save, it will duplicate to the master, if necessary.
        // A noteworthy example is when both a resident and coach have answers, and the resident resets their input
        // source, leaving only the coach data. Without this extra save, their steps will never trigger a master
        // otherwise and leave the coach in an infinite loop until the resident creates new answers.
        if (! $completedSubStep->wasRecentlyCreated) {
            $completedSubStep->save();
        }

        return $completedSubStep;
    }

    /**
     * Incomplete a step for a building.
     *
     * @return void
     */
    public function incomplete()
    {
        optional(CompletedSubStep::allInputSources()->where([
            'sub_step_id' => $this->subStep->id,
            'input_source_id' => $this->inputSource->id,
            'building_id' => $this->building->id,
        ])->first())->delete();
    }
}