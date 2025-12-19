<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\HoomdossierSession;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Helpers\MyRegulationHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Helpers\ToolHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\Step;
use App\Models\User;
use App\Scopes\GetValueScope;
use App\Services\BuildingCoachStatusService;
use App\Services\DumpService;
use App\Services\Kengetallen\KengetallenService;
use App\Services\Models\AlertService;
use App\Services\UserActionPlanAdviceService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Support\Str;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class UserReportController extends Controller
{
    /**
     * TESTING only.
     */
    public function index(KengetallenService $kengetallenService, Cooperation $userCooperation, ?string $scanShort = null)
    {
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
            ->createHeaderStructure($short);

        $dump = [];
        // Retrieve headers AFTER the dump is done, as conditionally incorrect data will be removed.
        $dump = $dumpService->generateDump();
        $headers = $dumpService->headerStructure;

        // So we don't use the initial headers (currently). Therefore, we anonymize, as then we only have to unset
        // the first five keys.
        unset(
            $dump[0],
            $dump[1],
            $dump[2],
            $dump[3],
            $dump[4],
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

        $measureSteps = [];
        $smallMeasureAdvices = [
            'small-measures' => [],
            'cooperation-measures' => [],
            'custom-measures' => [],
        ];

        $categorizedAdvices = $user->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->cooperationMeasureForType(CooperationMeasureApplicationHelper::SMALL_MEASURE, $inputSource)
            ->with(['userActionPlanAdvisable' => fn ($q) => $q->withoutGlobalScope(GetValueScope::class)])
            ->getCategorized()
            ->map(function ($advices) use (&$measureSteps, &$smallMeasureAdvices) {
                return $advices->map(function ($userActionPlanAdvice) use (&$measureSteps, &$smallMeasureAdvices) {
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

                        if ($userActionPlanAdvice->userActionPlanAdvisable instanceof MeasureApplication) {
                            $measureSteps[] = $userActionPlanAdvice->userActionPlanAdvisable->step_id;

                            $smallMeasureAdvicestep = Step::findByShort('small-measures');
                            if ($userActionPlanAdvice->userActionPlanAdvisable->step_id === $smallMeasureAdvicestep->id) {
                                $smallMeasureAdvices['small-measures'][] = $userActionPlanAdvice;
                            }
                        } else {
                            if ($userActionPlanAdvice->userActionPlanAdvisable instanceof CooperationMeasureApplication) {
                                $smallMeasureAdvices['cooperation-measures'][] = $userActionPlanAdvice;
                            } else {
                                $smallMeasureAdvices['custom-measures'][] = $userActionPlanAdvice;
                            }
                        }
                    }

                    return $userActionPlanAdvice;
                });
            });


        // Some extra code only needs to happen if we're building the extensive PDF
        if ($short === ToolHelper::STRUCT_PDF_QUICK) {
            // This piece of code, it doesn't make me proud. However, it's a necessary evil, as we need
            // all the steps that have been completed, but some measures are mapped to old steps. We need to
            // correct these.
            if (! empty($measureSteps)) {
                $measureSteps = array_unique($measureSteps);
                $shorts = [];
                $reverseStruct = [];
                foreach (StepHelper::STEP_COMPLETION_MAP as $parentShort => $subShorts) {
                    $shorts = array_merge($shorts, $subShorts);

                    foreach ($subShorts as $subShort) {
                        $reverseStruct[$subShort] = $parentShort;
                    }
                }
                $steps = Step::findByShorts($shorts);

                foreach ($steps as $step) {
                    if (in_array($step->id, $measureSteps)) {
                        // Remove the old step from the array
                        $index = array_search($step->id, $measureSteps);
                        unset($measureSteps[$index]);

                        // Add the new one if needed
                        $parentStep = Step::findByShort($reverseStruct[$step->short]);
                        if (! in_array($parentStep->id, $measureSteps)) {
                            $measureSteps[] = $parentStep->id;
                        }
                    }
                }
            }
        }

        $adviceComments = $user->userActionPlanAdviceComments()
            ->allInputSources()
            ->with('inputSource')
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->orderBy('updated_at')
            ->get()
            ->keyBy('input_source_id');

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuilding($building);
        $connectedCoachNames = User::whereIn('id', $connectedCoaches->pluck('coach_id')->toArray())
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS full_name")
            ->pluck('full_name')
            ->toArray();

        $alerts = AlertService::init()
            ->inputSource($inputSource)
            ->building($building)
            ->getAlerts();

        $subsidyRegulations = MyRegulationHelper::getRelevantRegulations(
            $building,
            $inputSource
        )[RegulationService::SUBSIDY] ?? [];

        $kengetallenService = $kengetallenService
            ->forInputSource($inputSource)
            ->forBuilding($building);
        // https://github.com/mccarlosen/laravel-mpdf
        // To style container margins of the PDF, see config/pdf.php
        //return view('cooperation.pdf.user-report.index', compact(
        //    'scanShort',
        //    'userCooperation',
        //    'building',
        //    'user',
        //    'inputSource',
        //    'connectedCoachNames',
        //    'headers',
        //    'simpleDump',
        //    'expertDump',
        //    'coachHelp',
        //    'categorizedAdvices',
        //    'measureSteps',
        //    'smallMeasureAdvices',
        //    'adviceComments',
        //    'alerts',
        //    'subsidyRegulations',
        //    'kengetallenService'
        //));

        return LaravelMpdf::loadView('cooperation.pdf.user-report.index', compact(
            'scanShort',
            'userCooperation',
            'building',
            'user',
            'inputSource',
            'connectedCoachNames',
            'headers',
            'simpleDump',
            'expertDump',
            'coachHelp',
            'categorizedAdvices',
            'measureSteps',
            'smallMeasureAdvices',
            'adviceComments',
            'alerts',
            'subsidyRegulations',
            'kengetallenService'
        ))->stream();
    }
}
