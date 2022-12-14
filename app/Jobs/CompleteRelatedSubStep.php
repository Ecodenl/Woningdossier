<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CompleteRelatedSubStep implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public SubStep $subStep;
    public Building $building;
    public InputSource $inputSource;

    public function __construct(SubStep $subStep, Building $building, InputSource $inputSource)
    {
        $this->subStep = $subStep;
        $this->building = $building;
        $this->inputSource = $inputSource;
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
                $query->select('*')->from('completed_sub_steps AS css')->where('css.sub_step_id',
                    'sub_steppables.sub_step_id')->where('input_source_id', $inputSource->id)->where('building_id',
                    $building->id);
            })->groupBy('sub_step_id')
            ->pluck('sub_step_id')
            ->toArray();

        if (! empty($subStepIds)) {
            $subSteps = SubStep::findMany($subStepIds);

            foreach ($subSteps as $subStep) {

            }
        }
    }
}