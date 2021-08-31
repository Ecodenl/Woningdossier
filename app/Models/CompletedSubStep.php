<?php

namespace App\Models;

use App\Events\StepDataHasBeenChanged;
use App\Helpers\Hoomdossier;
use App\Helpers\StepHelper;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSubStep extends Model
{

    use GetMyValuesTrait,
        GetValueTrait;

    protected $fillable = ['sub_step_id', 'building_id', 'input_source_id'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($completedSubStep) {
            // Check if this sub step finished the step
            $subStep = $completedSubStep->subStep;

            if ($subStep instanceof SubStep) {
                $step = $subStep->step;
                $inputSource = $completedSubStep->inputSource;
                $building = $completedSubStep->building;

                if ($step instanceof Step && $inputSource instanceof InputSource && $building instanceof Building) {
                    $allCompletedSubStepIds = CompletedSubStep::forInputSource($inputSource)
                        ->forBuilding($building)
                        ->whereHas('subStep', function ($query) use ($step) {
                            $query->where('step_id', $step->id);
                        })
                        ->pluck('sub_step_id')->toArray();

                    $allSubStepIds = $step->subSteps()->pluck('id')->toArray();

                    $diff = array_diff($allSubStepIds, $allCompletedSubStepIds);

                    if (empty ($diff)) {
                        // The sub step that has been completed finished up the set, so we complete the main step
                        StepHelper::complete($step, $building, $inputSource);

                        // Trigger a recalculate if the tool is now complete
                        // TODO: Refactor this
                        if ($building->hasCompletedQuickScan()) {
                            StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
                        }
                    } else {
                        // We didn't fill in each sub step. But, it might be that there's sub steps with conditions
                        // that we didn't get. Let's check
                        $leftoverSubSteps = SubStep::findMany($diff);

                        $cantSee = 0;
                        foreach ($leftoverSubSteps as $subStep) {
                            if (! $building->user->account->can('show', $subStep)) {
                                ++$cantSee;
                            }
                        }

                        if ($cantSee === $leftoverSubSteps->count()) {
                            // Conditions "passed", so we complete!
                            StepHelper::complete($step, $building, $inputSource);

                            // Trigger a recalculate if the tool is now complete
                            // TODO: Refactor this
                            if ($building->hasCompletedQuickScan()) {
                                StepDataHasBeenChanged::dispatch($step, $building, Hoomdossier::user());
                            }
                        }
                    }
                }
            }
        });
    }

    public function subStep(): BelongsTo
    {
        return $this->belongsTo(SubStep::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
