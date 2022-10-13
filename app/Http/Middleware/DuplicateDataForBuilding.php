<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Jobs\CloneOpposingInputSource;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\Models\NotificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DuplicateDataForBuilding
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $opposingInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $completedSubStepsExists = $building
            ->completedSubSteps()
            ->forInputSource($inputSource)
            ->exists();


        // when the current user for its current input source has no completed SUB steps
        // we will try to duplicate the data from a opposing input source, in this case the master.
        if ($completedSubStepsExists === false && Hoomdossier::user()->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COACH, RoleHelper::ROLE_RESIDENT])) {
            Log::debug("User {$building->id} has no completed sub steps for input source {$inputSource->short}");
            // 9 out of 10 times the completed sub steps WILL exists for the inputSource
            // so ill do the other query inside here to prevent 1 extra query each request.

            // now check if the "opposing" input source (master) completed some steps
            $opposingInputSourceCompletedSubStepExists = $building
                ->completedSubSteps()
                ->forInputSource($opposingInputSource)
                ->exists();

            if ($opposingInputSourceCompletedSubStepExists) {
                Log::debug("User {$building->id} its opposing input source HAS completed sub steps for input source {$inputSource->short}, starting to clone..");
                // we will set the notification before its picked up by the queue
                // otherwise the user would get weird ux
                NotificationService::init()
                    ->forBuilding($building)
                    ->forInputSource($inputSource)
                    ->setType(CloneOpposingInputSource::class)
                    ->setActive();
                CloneOpposingInputSource::dispatch($building, $inputSource, $opposingInputSource);
            }
        }

        return $next($request);
    }
}
