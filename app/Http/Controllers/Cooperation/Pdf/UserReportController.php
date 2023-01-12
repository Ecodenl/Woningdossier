<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\Arr;
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
use App\Services\UserActionPlanAdviceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use Mpdf\Mpdf;

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

        $dumpService = DumpService::init()
            ->user($user)
            ->inputSource($inputSource)
            ->defaultEmptyAnswer()
            ->withUnits()
            ->dontFormatArrayAnswers()
            ->anonymize() // See comment above unset below
            ->createHeaderStructure($short, false);

        $headers = $dumpService->headerStructure;
        $dump = $dumpService->generateDump();

        // So we don't use the initial headers (currently). Therefore, we anonymize, as then we only have to unset
        // the first four keys.
        unset(
            $dump[0],
            $dump[1],
            $dump[2],
            $dump[3],
        );

        // Manipulate the dump so it's categorized by step
        $dump = Arr::undot($dump);

        $categorizedTotals = [
            UserActionPlanAdviceService::CATEGORY_TO_DO => [
                'costs' => [
                    'from' => 0,
                    'to' => 0,
                ],
                'savings' => 0,
            ],
            UserActionPlanAdviceService::CATEGORY_LATER => [
                'costs' => [
                    'from' => 0,
                    'to' => 0,
                ],
                'savings' => 0,
            ],
        ];

        $categorizedAdvices = $user->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->withoutDeletedCooperationMeasureApplications($inputSource)
            ->with(['userActionPlanAdvisable' => fn ($q) => $q->withoutGlobalScope(GetValueScope::class)])
            ->getCategorized()
            ->map(function ($advices) use (&$categorizedTotals) {
                return $advices->map(function ($userActionPlanAdvice) use (&$categorizedTotals) {
                    if ($userActionPlanAdvice->category !== UserActionPlanAdviceService::CATEGORY_COMPLETE) {
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

                        if (! is_null($from) || ! is_null($to)) {
                            if (is_null($from) && ! is_null($to)) {
                                $from = $to;
                            } elseif (! is_null($from) && is_null($to)) {
                                $to = $from;
                            }

                            $categorizedTotals[$userActionPlanAdvice->category]['costs']['from'] += $from;
                            $categorizedTotals[$userActionPlanAdvice->category]['costs']['to'] += $to;
                        }

                        $categorizedTotals[$userActionPlanAdvice->category]['savings'] += $userActionPlanAdvice->savings_money;
                    }

                    return $userActionPlanAdvice;
                });
            });

        // Format ready for in blade
        foreach ($categorizedTotals as $category => $totals) {
            if ($totals['costs']['from'] === $totals['costs']['to']) {
                $categorizedTotals[$category]['costs'] = $totals['costs']['from'];
            } else {
                $categorizedTotals[$category]['costs'] = NumberFormatter::range($totals['costs']['from'], $totals['costs']['to']);
            }
        }

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

        // https://github.com/mccarlosen/laravel-mpdf
        // To style container margins of the PDF, see config/pdf.php
        return LaravelMpdf::loadView('cooperation.pdf.user-report.index', compact(
            'scanShort',
            'cooperation',
            'building',
            'user',
            'connectedCoachNames',
            'headers',
            'dump',
            'categorizedAdvices',
            'categorizedTotals',
            'adviceComments',
        ))->stream();
    }
}
