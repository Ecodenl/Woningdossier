<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\UserActionPlanAdvice;
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
        $this->cooperationMeasureApplication = $cooperationMeasureApplication;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
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
                            // Get all advices for this user id
                            $advicesForUserId = $this->cooperationMeasureApplication
                                ->userActionPlanAdvices()
                                ->allInputSources()
                                ->where('user_id', $user->id);
                            $inputSourceIds = $advicesForUserId->where('input_source_id', '!=', $masterInputSource->id)
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
                                $adviceForInputSource = $advicesForUserId->where('input_source_id', $inputSourceId)->first();
                                if ($adviceForInputSource instanceof UserActionPlanAdvice) {
                                    // Update the advice from the cooperation measure to the custom measure
                                    $adviceForInputSource->update([
                                        'user_action_plan_advisable_type' => CustomMeasureApplication::class,
                                        'user_action_plan_advisable_id' => $customMeasure->id,
                                    ]);
                                }
                            }

                            // The master updates the custom measure automatically, but it doesn't update
                            // the user action plan advice. It instead generates a new one. We delete the old advice if it
                            // exists.
                            $adviceForMaster = UserActionPlanAdvice::forUser($user)
                                ->forInputSource($masterInputSource)
                                ->withInvisible()
                                ->whereHasMorph('userActionPlanAdvisable', CooperationMeasureApplication::class,
                                    fn($query) => $query->withTrashed()->where('id', $this->cooperationMeasureApplication->id)
                                )
                                ->first();

                            if ($adviceForMaster instanceof UserActionPlanAdvice) {
                                $adviceForMaster->delete();
                            }

                            $processedUserIds[] = $user->id;
                        }
                    }
                });
        }

        // and truly delete the row from the database
        $this->cooperationMeasureApplication->forceDelete();
    }
}
