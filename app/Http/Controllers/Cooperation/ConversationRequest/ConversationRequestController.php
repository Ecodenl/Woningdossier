<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Services\PrivateMessageService;

class ConversationRequestController extends Controller
{
    /**
     * Show the form.
     *
     * @param  Cooperation  $cooperation
     * @param  null  $option
     * @param  null  $measureApplicationShort
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation, $requestType = null, $measureApplicationShort = null)
    {
        $userAlreadyHadContactWithCooperation = PrivateMessage::public()->conversation(HoomdossierSession::getBuilding())->first() instanceof PrivateMessage;

        // if the user is observing, he has nothing to do here.
        if (HoomdossierSession::isUserObserving()) {
            return redirect()->route('cooperation.tool.my-plan.index');
        }
        $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

        // set the measure application name if there is a measure application
        $measureApplicationName = $measureApplication instanceof MeasureApplication ? $measureApplication->measure_name : null;
        $selectedOption = $requestType;
        $shouldShowOptionList = is_null($requestType) ? true : false;
        $userDidNotAllowAccessToBuilding = !PrivateMessage::allowedAccess(HoomdossierSession::getBuilding(true));
        // why make it simple and clean, when you can't ?
        if ($requestType == PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION) {
            $title = __('conversation-requests.index.request-coach-conversation');
        }
        elseif (is_null($measureApplicationName)) {
            $title = __('conversation-requests.index.form.no-measure-application-name-title');
        } else {
            $title =  __('conversation-requests.index.form.title', ['measure_application_name' => $measureApplicationName]);
        }


        return view('cooperation.conversation-requests.index', compact('selectedOption', 'userAlreadyHadContactWithCooperation', 'measureApplicationName', 'shouldShowOptionList', 'title', 'userDidNotAllowAccessToBuilding', 'userAlreadyHadContactWithCooperation'));
    }

    /**
     * Save the conversation request for whatever the conversation request may be.
     *
     * @param  ConversationRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ConversationRequest $request, Cooperation $cooperation)
    {
        PrivateMessageService::createConversationRequest(HoomdossierSession::getBuilding(true), Hoomdossier::user(), $request);

        HoomdossierSession::getBuilding(true)->setStatus('pending');

        $successMessage = HoomdossierSession::getInputSource(true)->short == InputSource::RESIDENT_SHORT ?  __('conversation-requests.store.success.'.InputSource::RESIDENT_SHORT, ['url' => route('cooperation.my-account.messages.index', compact('cooperation'))]) : __('conversation-requests.store.success.'.InputSource::COACH_SHORT);

        return redirect(route('cooperation.tool.my-plan.index'))
            ->with('success', $successMessage);
    }
}
