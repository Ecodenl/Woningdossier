<?php

namespace App\Jobs;

use App\Events\CustomMeasureApplicationChanged;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
use App\Services\MappingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HandleCooperationMeasureApplicationDeletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $cooperationMeasureApplication;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CooperationMeasureApplication $cooperationMeasureApplication)
    {
        $this->queue = Queue::APP;
        $this->cooperationMeasureApplication = $cooperationMeasureApplication;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // We can't delete the cooperation measure application straight away. We need to check
        // if it's used in any UserActionPlanAdvices.

        // If it's an extensive measure, we'll just delete the whole clown show
        if ($this->cooperationMeasureApplication->is_extensive_measure) {
            DB::table('user_action_plan_advices')
                ->where('user_action_plan_advisable_type', CooperationMeasureApplication::class)
                ->where('user_action_plan_advisable_id', $this->cooperationMeasureApplication->id)
                ->delete();
        } else {
            // We need the master
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

            $processedUserIds = [];

            $this->cooperationMeasureApplication
                ->userActionPlanAdvices()
                ->allInputSources()
                ->with(['user.building'])
                ->chunkById(100, function ($advices) use ($masterInputSource, &$processedUserIds) {
                    foreach ($advices as $advice) {
                        // The master input source makes this a massive pain
                        // First we check if we haven't already processed this set
                        // We need a valid building for this to work
                        $user = $advice->user;
                        if (! in_array($user->id, $processedUserIds) && $user->building instanceof Building) {
                            // Get all advices for this user id.
                            // We don't need to remove the visible scope, that's already done in the relation.
                            $advicesForUserId = $this->cooperationMeasureApplication
                                ->userActionPlanAdvices()
                                ->allInputSources()
                                ->where('user_id', $user->id);

                            // Get all input source IDs that are not the master (without affecting the original query).
                            $inputSourceIds = $advicesForUserId->clone()
                                ->where('input_source_id', '!=', $masterInputSource->id)
                                ->pluck('input_source_id');

                            $hash = Str::uuid();
                            $createData = [
                                'building_id' => $user->building->id,
                                'name' => $this->cooperationMeasureApplication->name,
                                'info' => $this->cooperationMeasureApplication->info,
                                'hash' => $hash,
                            ];

                            foreach ($inputSourceIds as $inputSourceId) {
                                $createData['input_source_id'] = $inputSourceId;

                                // Create a custom measure with the data of the cooperation measure
                                $customMeasure = CustomMeasureApplication::create($createData);
                                // Get the advice for this input source (without affecting the original query).
                                $adviceForInputSource = $advicesForUserId->clone()
                                    ->where('input_source_id', $inputSourceId)
                                    ->first();
                                if ($adviceForInputSource instanceof UserActionPlanAdvice) {
                                    // Update the advice from the cooperation measure to the custom measure
                                    $adviceForInputSource->update([
                                        'user_action_plan_advisable_type' => CustomMeasureApplication::class,
                                        'user_action_plan_advisable_id' => $customMeasure->id,
                                    ]);
                                }
                            }

                            // We want to do the mapping as last, since it might get reverted by a change in a related
                            // advice. If the measure is set, we know we can get a sibling.
                            if (isset($customMeasure)) {
                                $service = MappingService::init()->from($this->cooperationMeasureApplication);

                                // Check if the cooperation has mappings
                                if ($service->mappingExists()) {
                                    $from = $customMeasure->getSibling($masterInputSource);

                                    foreach ($service->resolveMapping() as $mapping) {
                                        $newMapping = $mapping->replicate();
                                        $newMapping->from_model_type = \App\Models\CustomMeasureApplication::class;
                                        $newMapping->from_model_id = $from->id;
                                        $newMapping->save();
                                    }

                                    // Ensure we refresh the regulations for the master
                                    CustomMeasureApplicationChanged::dispatch($from);
                                }
                            }

                            // The master updates the custom measure automatically, but it doesn't update
                            // the user action plan advice. It instead generates a new one. We delete the old advice if
                            // it exists.
                            DB::table('user_action_plan_advices')
                                ->where('user_id', $user->id)
                                ->where('input_source_id', $masterInputSource->id)
                                ->where('user_action_plan_advisable_type', CooperationMeasureApplication::class)
                                ->where('user_action_plan_advisable_id', $this->cooperationMeasureApplication->id)
                                ->delete();

                            $processedUserIds[] = $user->id;
                        }
                    }
                });
        }

        // and truly delete the row from the database
        $this->cooperationMeasureApplication->forceDelete();
    }
}
