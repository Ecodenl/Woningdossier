<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Events\StepDataHasBeenChangedEvent;
use App\Helpers\Calculator;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\MyPlanHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyPlanRequest;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\FileTypeCategory;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserActionPlanAdviceComments;
use App\Scopes\AvailableScope;
use App\Services\FileStorageService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function index()
    {

        dd(FileStorageService::isFileTypeBeingProcessedForUser(FileType::whereShort('pdf-report')->first(), Hoomdossier::user(), HoomdossierSession::getInputSource(true)));

        $anyFilesBeingProcessed = FileStorage::forMe()->withExpired()->beingProcessed()->count();

        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $advices = UserActionPlanAdvice::getCategorizedActionPlan($buildingOwner, HoomdossierSession::getInputSource(true));
        $actionPlanComments = UserActionPlanAdviceComments::forMe()->get();

        // so we can determine wheter we will show the actionplan button
        $buildingHasCompletedGeneralData = $building->hasCompleted(Step::where('slug', 'general-data')->first());

        $pdfReportFileType = FileType::where('short', 'pdf-report')
            ->with(['files' => function ($query) {
                $query->forMe();
            }])->first();

        $file = $pdfReportFileType->files->first();


        return view('cooperation.tool.my-plan.index', compact(
            'advices', 'coachCommentsByStep', 'actionPlanComments', 'file',
            'anyFilesBeingProcessed', 'pdfReportFileType', 'buildingHasCompletedGeneralData'
        ));
    }

    /**
     * Store a comment for the my plan page for the current inputsource on the owner of the building.
     *
     * @param MyPlanRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeComment(MyPlanRequest $request)
    {
        $comment = $request->get('comment');
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        // update or create the comment
        UserActionPlanAdviceComments::updateOrCreate(
            [
                'input_source_id' => HoomdossierSession::getInputSource(),
                'user_id' => $buildingOwner->id,
            ],
            [
                'comment' => $comment,
            ]
        );

        return redirect()->route('cooperation.tool.my-plan.index');
    }

    public function store(Request $request)
    {
        $sortedAdvices = [];

        $myAdvices = $request->input('advice', []);
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        foreach ($myAdvices as $adviceId => $data) {
            $advice = UserActionPlanAdvice::find($adviceId);

            // set the statements in variable for better readability
            $actionPlanExists = $advice instanceof UserActionPlanAdvice;
            $inputSourceIdIsInputSourceOrUserIsObserving = $advice->input_source_id == HoomdossierSession::getInputSource() || HoomdossierSession::isUserObserving();
            $buildingOwnerIdIsUserId = $buildingOwner->id == $advice->user_id;

            // check if the advice exists, if the input source id is the current input source and if the buildingOwner id is the user id
            // check if the action plan exists, if the input source id from the advice is the inputsource itself or if the user is observing and the buildingOwner is the userId
            if ($actionPlanExists && $inputSourceIdIsInputSourceOrUserIsObserving && $buildingOwnerIdIsUserId) {

                // if the user isnt observing a other building we allow changes, else we dont.
                if (HoomdossierSession::isUserObserving() == false) {
                    MyPlanHelper::saveUserInterests($request, $advice);
                }

                // check if a user is interested in a measure
                //if (MyPlanHelper::isUserInterestedInMeasure($advice->step)) {
                if ($advice->planned) {

                    $year = $advice->getYear();

                    // if its a string, the $year contains 'geen jaartal'
                    if (is_string($year)) {
                        $costYear = Carbon::now()->year;
                    } else {
                        $costYear = $year;
                    }
                    if (! array_key_exists($year, $sortedAdvices)) {
                        $sortedAdvices[$year] = [];
                    }

                    // get step from advice
                    $step = $advice->step;

                    if (! array_key_exists($step->name, $sortedAdvices[$year])) {
                        $sortedAdvices[$year][$step->name] = [];
                    }

                    $sortedAdvices[$year][$step->name][] = [
                        'interested' => $advice->planned,
                        'advice_id' => $advice->id,
                        'measure' => $advice->measureApplication->measure_name,
                        'measure_short' => $advice->measureApplication->short,
                        // In the table the costs are indexed based on the advice year
                        // Now re-index costs based on user planned year in the personal plan
                        'costs' => NumberFormatter::round(Calculator::indexCosts($advice->costs, $costYear)),
                        'savings_gas' => is_null($advice->savings_gas) ? 0 : NumberFormatter::round($advice->savings_gas),
                        'savings_electricity' => is_null($advice->savings_electricity) ? 0 : NumberFormatter::round($advice->savings_electricity),
                        'savings_money' => is_null($advice->savings_money) ? 0 : NumberFormatter::round(Calculator::indexCosts($advice->savings_money, $costYear)),
                    ];
                }
            }
        }

        ksort($sortedAdvices);

        return response()->json($sortedAdvices);
    }
}
