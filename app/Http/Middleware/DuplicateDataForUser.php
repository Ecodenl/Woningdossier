<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Jobs\CloneOpposingInputSource;
use App\Models\InputSource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DuplicateDataForUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = Hoomdossier::user();
        $inputSource = HoomdossierSession::getInputSource(true);
        $opposingInputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        $completedSubStepsExists = $user
            ->building
            ->completedSubSteps()
            ->forInputSource($inputSource)
            ->exists();

        // when the current user for its current input source has no completed SUB steps
        // we will try to duplicate the data from a opposing input source, in this case the master.
        if ($completedSubStepsExists === false) {
            Log::debug("User {$user->id} has no completed sub steps");
            // 9 out of 10 times the completed sub steps WILL exists for the inputSource
            // so ill do the other query inside here to prevent 1 extra query each request.

            // now check if the "opposing" input source (master) completed some steps
            $opposingInputSourceCompletedSubStepExists = $user
                ->building
                ->completedSubSteps()
                ->forInputSource($opposingInputSource)
                ->exists();

            if ($opposingInputSourceCompletedSubStepExists) {
                Log::debug("User {$user->id} its opposing input source HAS completed sub steps, starting to clone..");
                CloneOpposingInputSource::dispatchNow($user, $inputSource, $opposingInputSource);
            }
        }

        return $next($request);
    }
}
