<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Helpers\MyPlanHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyPlanRequest;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserActionPlanAdviceComments;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function index()
    {
        $inputSource = HoomdossierSession::getInputSource(true);
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;
        $advices = UserActionPlanAdviceService::getCategorizedActionPlan($buildingOwner, $inputSource);
        $actionPlanComments = UserActionPlanAdviceComments::forMe()->get();
        $anyFilesBeingProcessed = FileStorage::forMe()->withExpired()->beingProcessed()->count();

        // so we can determine whether we will show the actionplan button
        $buildingHasCompletedGeneralData = $building->hasCompleted(Step::where('slug', 'general-data')->first());

        // get the pdf report, and the report file for the building owner with the current input source.
        $pdfReportFileType = FileType::where('short', 'pdf-report')
            ->with(['files' => function ($query) use ($buildingOwner, $inputSource) {
                $query->forMe($buildingOwner)->forInputSource($inputSource);
            }])->first();

        $file = $pdfReportFileType->files->first();

        // get the input sources that have an action plan for the current building
        // and filter out the current one
        $inputSourcesForPersonalPlanModal = UserActionPlanAdviceService::availableInputSourcesForActionPlan($buildingOwner)
            ->filter(function ($inputSourceForActionPlan) use ($inputSource) {
                return $inputSourceForActionPlan->short !== $inputSource->short;
            });

        // so we have to create modals, with personal plan info within.
        // but, only for different input sources then the current one.
        $personalPlanForVariousInputSources = [];
        foreach ($inputSourcesForPersonalPlanModal as $inputSource) {
            $personalPlanForVariousInputSources[$inputSource->name] = UserActionPlanAdviceService::getPersonalPlan($buildingOwner, $inputSource);
        }

        return view('cooperation.tool.my-plan.index', compact(
            'actionPlanComments', 'pdfReportFileType', 'file', 'inputSourcesForPersonalPlanModal', 'advices',
            'anyFilesBeingProcessed', 'reportFileTypeCategory', 'buildingHasCompletedGeneralData', 'personalPlanForVariousInputSources'
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
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);
        $buildingOwner = $building->user;
        $myAdvices = $request->input('advice', []);

        foreach ($myAdvices as $adviceId => $data) {
            $advice = UserActionPlanAdvice::find($adviceId);

            // set the statements in variable for better readability
            $actionPlanExists = $advice instanceof UserActionPlanAdvice;
            $inputSourceIdIsInputSourceOrUserIsObserving = $actionPlanExists && $advice->input_source_id == $inputSource->id || HoomdossierSession::isUserObserving();
            $buildingOwnerIdIsUserId = $actionPlanExists && $buildingOwner->id == $advice->user_id;

            // check if the advice exists, if the input source id is the current input source and if the buildingOwner id is the user id
            // check if the action plan exists, if the input source id from the advice is the inputsource itself or if the user is observing and the buildingOwner is the userId
            if ($inputSourceIdIsInputSourceOrUserIsObserving && $buildingOwnerIdIsUserId) {
                // if the user isnt observing a other building we allow changes, else we dont.
                if (false == HoomdossierSession::isUserObserving()) {
                    MyPlanHelper::saveUserInterests($advice, $request->input("advice.{$advice->id}"));
                }
            }
        }

        return response()->json(UserActionPlanAdviceService::getPersonalPlan($buildingOwner, $inputSource));
    }
}
