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
     *
     * @return void
     */
    public function complete()
    {
        CompletedSubStep::allInputSources()->firstOrCreate([
            'sub_step_id' => $this->subStep->id,
            'input_source_id' => $this->inputSource->id,
            'building_id' => $this->building->id,
        ]);
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