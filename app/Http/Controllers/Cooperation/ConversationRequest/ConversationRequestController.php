<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Services\PrivateMessageService;

class ConversationRequestController extends Controller
{
    /**
     * Show the form.
     *
     * @param string|null $requestType             Default: null
     * @param string|null $measureApplicationShort Default: null
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation, $requestType, $measureApplicationShort = null)
    {
        // if the user is observing, he has nothing to do here.
        if (HoomdossierSession::isUserObserving()) {
            return redirect()->route('cooperation.tool.my-plan.index');
        }

        $title = __('conversation-requests.index.request-coach-conversation');

        if (! is_null($measureApplicationShort)) {
            $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->firstOrFail();
            // set the measure application name if there is a measure application
            $measureApplicationName = $measureApplication->measure_name;
            $title = __('conversation-requests.index.form.title', ['measure_application_name' => $measureApplicationName]);
        }

        return view('cooperation.conversation-requests.index', compact('selectedOption', 'requestType', 'measureApplicationName', 'title'));
    }

    /**
     * Save the conversation request for whatever the conversation request may be.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ConversationRequest $request, Cooperation $cooperation)
    {
        PrivateMessageService::createConversationRequest(HoomdossierSession::getBuilding(true), Hoomdossier::user(), $request);

        HoomdossierSession::getBuilding(true)->setStatus('pending');

        $successMessage = __('conversation-requests.store.success.'.InputSource::COACH_SHORT);

        if (InputSource::RESIDENT_SHORT == HoomdossierSession::getInputSource(true)->short) {
            $successMessage = __('conversation-requests.store.success.'.InputSource::RESIDENT_SHORT, ['url' => route('cooperation.my-account.messages.index', compact('cooperation'))]);
        }

        return redirect(route('cooperation.tool.my-plan.index'))
            ->with('success', $successMessage);
    }
}
