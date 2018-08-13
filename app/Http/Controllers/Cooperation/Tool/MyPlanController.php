<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculator;
use App\Helpers\MeasureApplicationHelper;
use App\Helpers\MyPlanHelper;
use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Services\CsvExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{

    public function index()
    {

        $user = \Auth::user();
        $advices = UserActionPlanAdvice::getCategorizedActionPlan($user);

        $steps = Step::orderBy('order')->get();


        return view('cooperation.tool.my-plan.index', compact(
            'advices', 'steps'
        ));
    }

    public function export()
    {
        // get the data
        $user = \Auth::user();
        $advices = UserActionPlanAdvice::getCategorizedActionPlan($user);

        // Column names
        $headers = [
            __('woningdossier.cooperation.tool.my-plan.csv-columns.year-or-planned'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.interest'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.measure'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.costs'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.savings-gas'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.savings-electricity'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.savings-costs'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.advice-year'),
            __('woningdossier.cooperation.tool.my-plan.csv-columns.costs-advice-year'),
        ];

        $userPlanData = [];

        foreach ($advices as $measureType => $stepAdvices) {
            foreach ($stepAdvices as $step => $advicesForStep) {
                foreach ($advicesForStep as $advice) {
                    // check if the planned year is set and if not use the year
                    $plannedYear = $advice->planned_year == null ? $advice->year : $advice->planned_year;
                    // check if a user is interested in the measure
                    $isInterested = $advice->planned == 1 ? __('default.yes') : __('default.no');
                    $costs = round($advice->costs);
                    $measure = $advice->measureApplication->measure_name;
                    $gasSavings = round($advice->savings_gas);
                    $electricitySavings = round($advice->savings_electricity);
                    $savingsInEuro = round($advice->savings_money);
                    $advicedYear = $advice->year;
                    $costsAdvisedYear = round(Calculator::reindexCosts($costs, $advicedYear, $plannedYear));

                    // push the plan data to the array
                    $userPlanData[$plannedYear][$measure] = [$plannedYear, $isInterested, $measure, $costs, $gasSavings, $electricitySavings, $savingsInEuro, $advicedYear, $costsAdvisedYear];
                }
            }
        }

        ksort($userPlanData);

        $userPlanData = array_flatten($userPlanData, 1);

        return CsvExportService::export($headers, $userPlanData, 'my-plan');
    }

    public function store(Request $request)
    {
        $sortedAdvices = [];

        $myAdvices = $request->input('advice', []);
        foreach ($myAdvices as $adviceId => $data) {
            $advice = UserActionPlanAdvice::find($adviceId);
            if ($advice instanceof UserActionPlanAdvice && $advice->user == \Auth::user()) {

                $myAdvice = $request->input('advice.' . $adviceId);

                // if the user checked the interested button

                $step = key($myAdvice);
                $requestPlannedYear = array_shift($myAdvice[$step]);
                $stepInterests = MyPlanHelper::STEP_INTERESTS[$step];


                $updates = [
                    'planned_year' => isset($requestPlannedYear) ? $requestPlannedYear : null
                ];

                $advice->update($updates);

                // get the planned year and current year
                $plannedYear = Carbon::create($requestPlannedYear);
                $currentYear = Carbon::now()->year(date('Y'));

                // get the current step
                $currentStep = Step::where('slug', $step)->first();

                $lowestPlannedYearForCurrentStep = UserActionPlanAdvice::where('step_id', $currentStep->id)->min('planned_year');
                $lowestPlannedYearForCurrentStep = Carbon::create($lowestPlannedYearForCurrentStep);

                // check if the user set the planned year
                if ($requestPlannedYear != null) {

                    // if the filled in year has a difference of 3 years lower then the current year
                    // we set the interest id to 2 or ja op termijn
                    if ($currentYear->diff($plannedYear)->y >= 3) {
                        $interestId = 2;
                    }
                    // if the filled in year has a difference of 3 years higher then the current year
                    // we set the interest id to 1 or yes in short term
                    else if ($currentYear->diff($plannedYear)->y <= 3) {
                        $interestId = 1;
                    }

                    // but, we will always look for the lowest year.
                    // so if the lowest year has a difference of 3 years lower then the current year
                    // we set the interest id to 1 or yes in short term
                    if ($currentYear->diff($lowestPlannedYearForCurrentStep)->y <= 3) {
                        $interestId = 1;
                    }

                    foreach ($stepInterests as $type => $interestInIds) {
                        foreach ($interestInIds as $interestInId) {
                            UserInterest::updateOrCreate(
                                [
                                    'interested_in_type' => $type,
                                    'interested_in_id' => $interestInId
                                ],
                                [
                                    'interest_id' => $interestId
                                ]
                            );
                        }
                    }
                }


                if (MyPlanHelper::isUserInterestedInMeasure($step)) {

                    $year = isset($advice->planned_year) ? $advice->planned_year : $advice->year;
                    if (is_null($year)) {
                        $year = $advice->getAdviceYear();
                    }
                    if (is_null($year)) {
                        $year = __('woningdossier.cooperation.tool.my-plan.no-year');
                        $costYear = Carbon::now()->year;
                    } else {
                        $costYear = $year;
                    }
                    if (!array_key_exists($year, $sortedAdvices)) {
                        $sortedAdvices[$year] = [];
                    }

                    $step = $advice->step;
                    if (!array_key_exists($step->name, $sortedAdvices[$year])) {
                        $sortedAdvices[$year][$step->name] = [];
                    }

                    $sortedAdvices[$year][$step->name][] = [
                        'interested' => true,
                        'advice_id' => $advice->id,
                        'measure' => $advice->measureApplication->measure_name,
                        // In the table the costs are indexed based on the advice year
                        // Now re-index costs based on user planned year in the personal plan
                        'costs' => Calculator::reindexCosts($advice->costs, $advice->year, $costYear),
                        'savings_gas' => is_null($advice->savings_gas) ? 0 : $advice->savings_gas,
                        'savings_electricity' => is_null($advice->savings_electricity) ? 0 : $advice->savings_electricity,
                        'savings_money' => is_null($advice->savings_money) ? 0 : $advice->savings_money,
                    ];

                }
            }
        }

        ksort($sortedAdvices);

        return response()->json($sortedAdvices);
    }
}
