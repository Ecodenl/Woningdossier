<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\Calculator;
use App\Http\Controllers\Controller;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{

	public function index(){
		$user = \Auth::user();
		$advices = UserActionPlanAdvice::getCategorizedActionPlan($user);
		//$advices = $user->actionPlanAdvices()->orderBy('year', 'asc')->get();
		$steps = Step::orderBy('order')->get();

		return view('cooperation.tool.my-plan.index', compact(
			'advices', 'steps'
		));
	}

    public function export()
    {


        // set the headers and stuff
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=my-plan.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // get the data
        $user = \Auth::user();
        $advices = UserActionPlanAdvice::getCategorizedActionPlan($user);

        // Column names
        $titles = [

                __('woningdossier.cooperation.tool.my-plan.csv-columns.year-or-planned'),
                __('woningdossier.cooperation.tool.my-plan.csv-columns.interest'),
                __('woningdossier.cooperation.tool.my-plan.csv-columns.measure'),
                __('woningdossier.cooperation.tool.my-plan.csv-columns.costs'),
                __('woningdossier.cooperation.tool.my-plan.csv-columns.savings-gas'),
                __('woningdossier.cooperation.tool.my-plan.csv-columns.savings-electricity'),
                __('woningdossier.cooperation.tool.my-plan.csv-columns.savings-costs'),
                __('woningdossier.cooperation.tool.my-plan.csv-columns.advice-year'),
        ];

        $userPlanData = [];

        foreach($advices as $measureType => $stepAdvices) {
            foreach($stepAdvices as $step => $advicesForStep) {
                foreach($advicesForStep as $advice) {

                    // check if the planned year is set and if not use the year
                    $plannedYear = $advice->planned_year == null ? $advice->year : $advice->planned_year;
                    // check if a user is interested in the measure
                    $isInterested = $advice->planned == 1 ? "Ja" : "Nee";
                    $costs = $advice->costs;
                    $measure = $advice->measureApplication->measure_name;
                    $gasSavings = $advice->savings_gas;
                    $electricitySavings = $advice->savings_electricity;
                    $savingsInEuro = $advice->savings_money;
                    $advicedYear = $advice->year;

                    // push the plan data to the array
                   $userPlanData[$plannedYear] = [ $plannedYear, $isInterested, $measure ,$costs, $gasSavings, $electricitySavings, $savingsInEuro, $advicedYear];
                }
            }
        }

        $callback = function () use ($userPlanData, $titles)
        {

            $file = fopen('php://output', 'w');
            fputcsv($file, $titles, ';');

            ksort($userPlanData);

            foreach ($userPlanData as $myPlan) {
                fputcsv($file, $myPlan, ';');
            }

            fclose($file);
        };

        return \Response::stream($callback, 200, $headers);
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
