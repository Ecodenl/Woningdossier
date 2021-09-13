<?php

namespace App\Http\Middleware;

use App\Helpers\QuickScanHelper;
use App\Models\SubStep;
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
        /** @var SubStep $subStep */
        $subStep = $request->route('subStep');

        $returnToNextStep = $request->user()->cannot('show', $subStep);

        if ($returnToNextStep) {
            // this indeed only covers the next step
            return redirect()->to(QuickScanHelper::getNextStepUrl($request->route('step'), $subStep));
        }

        return $next($request);
    }
}
