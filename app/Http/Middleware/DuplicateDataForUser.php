<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Jobs\CloneOpposingInputSource;
use App\Models\InputSource;
use Closure;
use Illuminate\Http\Request;

class DuplicateDataForUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = Hoomdossier::user();
        $inputSource = HoomdossierSession::getInputSource(true);

        $completedSubStepsExists = $user
            ->building
            ->completedSubSteps()
            ->forInputSource($inputSource)
            ->exists();

        // when the current user for its current input source has no completed SUB steps
        // we will try to duplicate the data from a opposing input source, in this case the master.
        if ($completedSubStepsExists === false) {
            CloneOpposingInputSource::dispatch($user, $inputSource, InputSource::findByShort(InputSource::MASTER_SHORT));
        }

        return $next($request);
    }
}
