<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConversationRequestController extends Controller
{

    /**
     * Show the form
     *
     * @param Cooperation $cooperation
     * @param null $option
     * @param null $measureApplicationShort
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation, $option = null, $measureApplicationShort = null)
    {
        $myOpenCoachConversationRequest = PrivateMessage::myOpenCoachConversationRequest()->first();

        if ($option == PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION && PrivateMessage::hasUserResponseToCoachConversationRequest() == false && $myOpenCoachConversationRequest != null) {
            return redirect()->route('cooperation.conversation-requests.edit', ['cooperation' => $cooperation, 'option' => PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION]);
        }


        $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

        // set the measure application name if there is a measure application
        $measureApplicationName = $measureApplication instanceof MeasureApplication ?  $measureApplication->measure_name : "";

        $selectedOption = $option;

        return view('cooperation.conversation-requests.index', compact('privateMessage', 'selectedOption', 'measureApplicationName'));
    }

    /**
     * Show the edit form to edit the coach conversation request
     *
     * This ONLY allows a user to edit his coach conversation request, we will redirect every other option / request_type back.
     *
     * @param Cooperation $cooperation
     * @param null $option
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Cooperation $cooperation, $option = null, $measureApplicationShort = null)
    {

        if ($option != PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION) {
            return redirect()->back();
        }

        // we get the intended message so if users wrote half a book they not lose it
        $intendedMessage = session('intendedMessage');

        $myOpenCoachConversationRequest = PrivateMessage::myOpenCoachConversationRequest()->first();
        if (!$myOpenCoachConversationRequest instanceof PrivateMessage){
        	return redirect()->route('cooperation.conversation-requests.index');
        }

        $selectedOption = $option;

        return view('cooperation.conversation-requests.edit', compact('myOpenCoachConversationRequest', 'selectedOption', 'intendedMessage'));
    }

    /**
     * Update a coach conversation request
     *
     * @param ConversationRequest $request
     * @param Cooperation $cooperation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ConversationRequest $request, Cooperation $cooperation)
    {
        $user = \Auth::user();
        $cooperationId = session('cooperation');

        $message = $request->get('message', '');
        $action = $request->get('action', '');
	    $allowAccess = $request->get('allow_access', '') == 'on';

        PrivateMessage::myOpenCoachConversationRequest()->update(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'title' => __('woningdossier.cooperation.conversation-requests.index.form.options.'.$action),
                'message' => $message,
                'to_cooperation_id' => $cooperationId,
                'from_user_id' => $user->id,
                'status' => PrivateMessage::STATUS_IN_CONSIDERATION,
                'request_type' => $action,
	            'allow_access' => $allowAccess,
            ]
        );

        return redirect()->back()->with('success', __('woningdossier.cooperation.conversation-requests.update.success', ['url' => route('cooperation.my-account.index', ['cooperation' => $cooperation->slug])]));
    }


    /**
     * Save the conversation request for whatever the conversation request may be
     *
     * @param ConversationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ConversationRequest $request, Cooperation $cooperation)
    {
        $action = $request->get('action', '');
        $message = $request->get('message', '');
	    $allowAccess = $request->get('allow_access', '') == 'on';

        $selectedOption = __('woningdossier.cooperation.conversation-requests.edit.form.'.$action);

        $myOpenCoachConversationRequest = PrivateMessage::myOpenCoachConversationRequest()->first();

        // if the the selected request type is a conversation and the user already has a conversation request with no answer and is still open
        // we will redirect him to the edit page for the particular conversation type, with his intended message he wanted to write
        if ($action == PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION && PrivateMessage::hasUserResponseToCoachConversationRequest() == false && $myOpenCoachConversationRequest != null) {
            return redirect()
                ->route('cooperation.conversation-requests.edit', ['cooperation' => $cooperation, 'option' => PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION])
                ->with('warning', __('woningdossier.cooperation.conversation-requests.update.warning', ['request_type' => $selectedOption]))
                ->with('intendedMessage', $message);
        }

        $measureApplicationName = $request->get('measure_application_name', '');

        $title = __('woningdossier.cooperation.conversation-requests.index.form.options.'.$action);

        // if the measureapplication name is not empty set it as a prefix in the title
        if ($measureApplicationName != "") {
            $title = $measureApplicationName ." - ".  __('woningdossier.cooperation.conversation-requests.index.form.options.'.$action);
        }

        $user = \Auth::user();
        $cooperationId = session('cooperation');

        PrivateMessage::create(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'title' => $title,
                'message' => $message,
                'to_cooperation_id' => $cooperationId,
                'from_user_id' => $user->id,
                'status' => PrivateMessage::STATUS_IN_CONSIDERATION,
                'request_type' => $action,
	            'allow_access' => $allowAccess,
            ]
        );

        $cooperation = Cooperation::find($cooperationId);

        return redirect()->route('cooperation.tool.my-plan.index')->with('success', __('woningdossier.cooperation.conversation-requests.store.success', ['url' => route('cooperation.my-account.index', ['cooperation' => $cooperation->slug])]));
    }

}
