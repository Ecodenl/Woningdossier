<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculator;
use App\Http\Controllers\Controller;
use App\Models\PrivateMessage;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Services\CsvExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{

	public function index()
    {
        $privateMessage = PrivateMessage::myCoachConversationRequest()->first();

		$user = \Auth::user();
		$advices = UserActionPlanAdvice::getCategorizedActionPlan($user);
		//$advices = $user->actionPlanAdvices()->orderBy('year', 'asc')->get();
		$steps = Step::orderBy('order')->get();

		return view('cooperation.tool.my-plan.index', compact(
			'advices', 'steps', 'privateMessage'
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

        foreach($advices as $measureType => $stepAdvices) {
            foreach($stepAdvices as $step => $advicesForStep) {
                foreach($advicesForStep as $advice) {
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
                   $userPlanData[$plannedYear][$measure] = [ $plannedYear, $isInterested, $measure ,$costs, $gasSavings, $electricitySavings, $savingsInEuro, $advicedYear, $costsAdvisedYear];
                }
            }
        }

        ksort($userPlanData);

        $userPlanData = array_flatten($userPlanData, 1);

        return CsvExportService::export($headers, $userPlanData, 'my-plan');
	}

	public function store(Request $request){
		$sortedAdvices = [];

		$myAdvices = $request->input('advice', []);
		foreach($myAdvices as $adviceId => $data){
			$advice = UserActionPlanAdvice::find($adviceId);
			if ($advice instanceof UserActionPlanAdvice && $advice->user == \Auth::user()){
				$updates = [
					'planned' => true,
					'planned_year' => array_key_exists('planned_year', $data) ? $data['planned_year'] : null,
				];
				if(!array_key_exists('planned', $data)){
					$updates['planned'] = false;
				}
				$advice->update($updates);

				if ($advice->planned){
					$year = isset($advice->planned_year) ? $advice->planned_year : $advice->year;
					if (is_null($year)) {
						$year = $advice->getAdviceYear();
					}
					if(is_null($year)) {
						$year = __('woningdossier.cooperation.tool.my-plan.no-year');
						$costYear = Carbon::now()->year;
					}
					else {
						$costYear = $year;
					}
					if(!array_key_exists($year, $sortedAdvices)){
						$sortedAdvices[$year] = [];
					}

					$step = $advice->step;
					if(!array_key_exists($step->name, $sortedAdvices[$year])){
						$sortedAdvices[$year][$step->name] = [];
					}

					$sortedAdvices[$year][$step->name][] = [
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
