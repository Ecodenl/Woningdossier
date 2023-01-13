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
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Str;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class UserReportController extends Controller
{
    /**
     * TESTING only.
     */
    public function index(Cooperation $cooperation, ?string $scanShort = null)
    {
        $GLOBALS['_cooperation'] = $cooperation;

        $scanShort ??= Scan::QUICK;
        if ($scanShort === Scan::LITE) {
            $short = ToolHelper::STRUCT_PDF_LITE;
        } else {
            $short = ToolHelper::STRUCT_PDF_QUICK;
        }

        // Always retrieve from master.
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        $dumpService = DumpService::init()
            ->user($user)
            ->inputSource($inputSource)
            ->setMode(DumpService::MODE_PDF)
            ->anonymize() // See comment above unset below
            ->createHeaderStructure($short, false);

        // Retrieve headers AFTER the dump is done, as conditionally incorrect data will be removed.
        $dump = $dumpService->generateDump();
        $headers = $dumpService->headerStructure;

        // So we don't use the initial headers (currently). Therefore, we anonymize, as then we only have to unset
        // the first four keys.
        unset(
            $dump[0],
            $dump[1],
            $dump[2],
            $dump[3],
        );

        $simpleDump = [];
        $expertDump = [];
        $expertStepShorts = Scan::findByShort(Scan::EXPERT)->steps()->pluck('short')->toArray();

        // Manipulate the dump so it's categorized by step.
        foreach ($dump as $key => $data) {
            // Step short is at first dot
            $parts = explode('.', $key, 2);
            $stepShort = $parts[0];
            $key = $parts[1];

            if (in_array($stepShort, $expertStepShorts)) {
                $expertDump[$stepShort][$key] = $data;
            } else {
                $simpleDump[$stepShort][$key] = $data;
            }
        }

        $coachHelp = [];

        if (array_key_exists('small-measures-lite', $simpleDump)) {
            $data = $simpleDump['small-measures-lite'];
            // In case of the lite scan, we need to move and merge some data.
            foreach ($data as $key => $value) {
                if (Str::contains($key, 'label_')) {
                    $coachHelp[$key] = $value;
                } elseif (Str::endsWith($key, '-coach-help')) {
                    // We don't want the coach help values here... so we move them.
                    $dataKey = Str::replaceLast('-coach-help', '', $key);
                    $coachHelp[$dataKey] = $value;
                    unset($data[$key]);
                } elseif (Str::endsWith($key, '-how')) {
                    // We do want the how values but not standalone.
                    if ($value !== __('cooperation/frontend/tool.no-answer-given')) {
                        $dataKey = Str::replaceLast('-how', '', $key);
                        $data[$dataKey] .= "; {$value}";
                    }
                    unset($data[$key]);
                }
            }
            $simpleDump['small-measures-lite'] = $data;

            // Now we need to remove empty labels from the coach help, else it looks weird...
            $previousKey = '';
            foreach ($coachHelp as $key => $value) {
                if (! empty($previousKey)) {
                    if (Str::contains($key, 'label_') && Str::contains($previousKey, 'label_')) {
                        unset($coachHelp[$previousKey]);
                    }
                }
                $previousKey = $key;
            }

            // Last short in the array, if this is also a label it's wrong
            if (Str::contains($previousKey, 'label_')) {
                unset($coachHelp[$previousKey]);
            }
        }

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
            'inputSource',
            'connectedCoachNames',
            'headers',
            'simpleDump',
            'expertDump',
            'coachHelp',
            'categorizedAdvices',
            'categorizedTotals',
            'adviceComments',
        ))->stream();
    }
}
