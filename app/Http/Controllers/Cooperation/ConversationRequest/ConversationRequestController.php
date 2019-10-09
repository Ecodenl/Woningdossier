<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Building;
use App\Models\Cooperation;
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
    public function index(Cooperation $cooperation, $option = null, $measureApplicationShort = null)
    {
        // if the user is observing, he has nothing to do here.
        if (HoomdossierSession::isUserObserving()) {
            return redirect()->route('cooperation.tool.my-plan.index');
        }
        $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

        // set the measure application name if there is a measure application
        $measureApplicationName = $measureApplication instanceof MeasureApplication ? $measureApplication->measure_name : null;
        $selectedOption = $option;
        $shouldShowOptionList = is_null($option) ? true : false;


        // why make it simple and clean, when you can't ?
        if ($option == PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION) {
            $title = __('conversation-requests.index.request-coach-conversation');
        }
        elseif (is_null($measureApplicationName)) {
            $title = __('conversation-requests.index.form.no-measure-application-name-title');
        } else {
            $title =  __('conversation-requests.index.form.title', ['measure_application_name' => $measureApplicationName]);
        }


        return view('cooperation.conversation-requests.index', compact('selectedOption', 'measureApplicationName', 'shouldShowOptionList', 'title'));
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
        PrivateMessageService::createConversationRequest(Hoomdossier::user(), $request);

        HoomdossierSession::getBuilding(true)->setStatus('pending');

        return redirect(route('cooperation.tool.my-plan.index'))
            ->with('success', __('conversation-requests.store.success', [
                'url' => route('cooperation.my-account.messages.index', compact('cooperation'))
            ]));
    }
}
