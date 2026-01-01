<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Models\Step;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function redirect(Cooperation $cooperation, Scan $scan): RedirectResponse
    {
        Log::debug('ScanController::redirect');

        if ($scan->short === Scan::EXPERT) {
            $step = Step::forScan($scan)->first();
            return to_route('cooperation.frontend.tool.expert-scan.index', compact('scan', 'step'));
        }
        Log::debug("Simple scan redirect, should this be happening ?");

        return to_route('cooperation.home');
    }
}
