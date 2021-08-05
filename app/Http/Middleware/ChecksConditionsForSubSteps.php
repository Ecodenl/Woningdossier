<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use Closure;

class ChecksConditionsForSubSteps
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $subStep = $request->route('subStep');

//        if ($request->user()->cannot('show', $subStep)) {
//             this indeed only covers the next step
//            return redirect()->to(QuickScanHelper::getNextStepUrl($request->route('step'), $subStep));
//        }

        return $next($request);
    }
}
