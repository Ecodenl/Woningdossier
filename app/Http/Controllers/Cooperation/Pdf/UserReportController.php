<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Helpers\ToolHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\User;
use App\Scopes\GetValueScope;
use App\Services\BuildingCoachStatusService;
use App\Services\DumpService;
use Barryvdh\DomPDF\Facade\Pdf;

class UserReportController extends Controller
{
    /**
     * TESTING only.
     */
    public function index(Cooperation $cooperation)
    {
        $GLOBALS['_cooperation'] = $cooperation;

        // TODO: Define short somehow
        $scanShort = Scan::QUICK;
        if ($scanShort === Scan::LITE) {
            $short = ToolHelper::STRUCT_PDF_LITE;
        } else {
            $short = ToolHelper::STRUCT_PDF_QUICK;
        }

        // Always retrieve from master
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $dumpService = DumpService::init()->inputSource($inputSource)
            ->user($user)
            ->createHeaderStructure($short, false);

        $headers = $dumpService->headerStructure;

        //$dump = $dumpService->generateDump();

        $categorizedAdvices = $user->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->withoutDeletedCooperationMeasureApplications($inputSource)
            ->with(['userActionPlanAdvisable' => fn ($q) => $q->withoutGlobalScope(GetValueScope::class)])
            ->getCategorized()
            ->map(function ($advices) {
                return $advices->map(function ($userActionPlanAdvice) {
                    $costs = $userActionPlanAdvice->costs ?? [];
                    $from = $costs['from'] ?? null;
                    $to = $costs['to'] ?? null;

                    if (! is_null($from)) {
                        NumberFormatter::round($from);
                    }
                    if (! is_null($to)) {
                        NumberFormatter::round($to);
                    }

                    $userActionPlanAdvice->costs = NumberFormatter::range($from, $to);
                    $userActionPlanAdvice->savings_money = NumberFormatter::round($userActionPlanAdvice->savings_money ?? 0);
                    return $userActionPlanAdvice;
                });
            });
        $adviceComments = $user->userActionPlanAdviceComments()
            ->allInputSources()
            ->with('inputSource')
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->get();

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = User::whereIn('id', $connectedCoaches->pluck('coach_id')->toArray())
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS full_name")
            ->pluck('full_name')
            ->toArray();

        return Pdf::loadView('cooperation.pdf.user-report.index', compact(
            'cooperation',
            'building',
            'user',
            'connectedCoachNames',
            //'headers',
            //'dump',
            'categorizedAdvices',
            'adviceComments',
        ))->stream();
    }
}
