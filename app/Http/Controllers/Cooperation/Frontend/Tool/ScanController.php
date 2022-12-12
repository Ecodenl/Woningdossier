<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Jobs\CloneOpposingInputSource;
use App\Models\Cooperation;
use App\Models\Notification;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function index(Cooperation $cooperation, Scan $scan, Step $step, SubStep $subStep)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);

        $notification = Notification::active()
            ->forBuilding(HoomdossierSession::getBuilding())
            ->forType(CloneOpposingInputSource::class)
            ->forInputSource($currentInputSource)->first();

        return view("cooperation.frontend.tool.simple-scan.index", compact('scan', 'step', 'subStep', 'notification', 'currentInputSource'));
    }

    public function redirect(Cooperation $cooperation, Scan $scan, Step $step, SubStep $subStep)
    {
        Log::debug('ScanController::show');

        if ($scan->short === Scan::EXPERT) {
            $step = Step::expert()->first();
            return redirect()->route('cooperation.frontend.tool.expert-scan.index', compact('scan', 'step'));
        }
        Log::debug("Simple scan redirect, should this be happening ?");

        return redirect()->route('cooperation.home');
    }
}
