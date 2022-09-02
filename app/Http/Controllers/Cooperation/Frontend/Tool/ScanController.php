<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function show(Cooperation $cooperation, Scan $scan)
    {
        Log::debug('ScanController::show');
        if ($scan->short === 'quick-scan') {
            $subStep = SubStep::ordered()->first();
            $step = $subStep->step;
            return redirect()->route('cooperation.frontend.tool.quick-scan.index', compact('scan', 'step', 'subStep'));
        }

        if ($scan->short === 'expert-scan') {
            // check if quick-scan is completed before proceeding
            $step = Step::expert()->first();
            return redirect()->route('cooperation.frontend.tool.expert-scan.index', compact('scan', 'step'));
        }
    }
}
