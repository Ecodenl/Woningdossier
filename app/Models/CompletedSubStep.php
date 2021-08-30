<?php

namespace App\Models;

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

                    if (empty (array_diff($allSubStepIds, $allCompletedSubStepIds))) {
                        // The sub step that has been completed finished up the set, so we complete the main step
                        StepHelper::complete($step, $building, $inputSource);
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
