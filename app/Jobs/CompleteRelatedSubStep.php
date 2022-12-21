<?php

namespace App\Jobs;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Services\Scans\ScanFlowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteRelatedSubStep implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public SubStep $subStep;
    public Building $building;
    public InputSource $inputSource;
    public InputSource $masterInputSource;

    public function __construct(SubStep $subStep, Building $building, InputSource $inputSource)
    {
        $this->subStep = $subStep;
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subStep = $this->subStep;
        $building = $this->building;
        $inputSource = $this->inputSource;
        $masterInputSource = $this->masterInputSource;

        Log::debug("Checking related (uncompleted) SubSteps for {$subStep->id}");

        // Simple but efficient query to get all uncompleted sub step IDs that use the same questions.
        $subStepIds = DB::table('sub_steppables')->select('sub_step_id')
            ->whereIn('sub_steppable_id', function ($query) use ($subStep) {
                $query->select('sub_steppable_id')
                    ->from('sub_steppables')
                    ->where('sub_steppable_type', ToolQuestion::class)
                    ->where('sub_step_id', $subStep->id);
            })->where('sub_steppable_type', ToolQuestion::class)
            ->where('sub_step_id', '!=', $subStep->id)
            ->whereNotExists(function ($query) use ($inputSource, $building) {
                $query->select('*')->from('completed_sub_steps AS css')
                    ->whereRaw('css.sub_step_id = sub_steppables.sub_step_id')
                    ->where('input_source_id', $inputSource->id)
                    ->where('building_id', $building->id);
            })->groupBy('sub_step_id')
            ->pluck('sub_step_id')
            ->toArray();

        if (! empty($subStepIds)) {
            Log::debug("Found related (uncompleted) SubSteps: " . json_encode($subStepIds));
            $subStepsToCheck = SubStep::findMany($subStepIds);

            $allConditions = $subStepsToSummarize->pluck('conditions')
                ->merge($subStepsToSummarize->pluck('subSteppables.*.conditions')->flatten(1))
                ->filter()
                ->flatten(1)
                ->all();

            // Get all conditions to get answers for
            $allConditions = [];
            foreach ($subStepsToCheck as $subStepToCheck) {
                $allConditions = array_merge($allConditions, $subStepToCheck->conditions ?? []);
            }

            $evaluator = ConditionEvaluator::init()
                ->building($building)
                ->inputSource($masterInputSource);

            $evaluator->setAnswers($evaluator->getToolAnswersForConditions($allConditions));

            // We don't use a scan but we need it for init...
            ScanFlowService::init($subStep->step->scan, $building, $inputSource)
                ->evaluateSubSteps($subStepsToCheck, $evaluator);
        }
    }
}