<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConversationRequestController extends Controller
{

    /**
     * Show the form
     *
     * @param Cooperation $cooperation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation, $option = null)
    {

        $myOpenCoachConversationRequest = PrivateMessage::myOpenCoachConversationRequest()->first();

        if ($option == PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION && PrivateMessage::hasUserResponseToCoachConversationRequest() == false && $myOpenCoachConversationRequest != null) {
            return redirect()->route('cooperation.conversation-requests.edit', ['cooperation' => $cooperation, 'option' => PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION]);

        }

        $selectedOption = $option;

        return view('cooperation.conversation-requests.index', compact('privateMessage', 'selectedOption'));
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
    public function edit(Cooperation $cooperation, $option = null)
    {
        if ($option != PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION) {
            return redirect()->back();
        }

        // we get the intended message so if users wrote half a book they not lose it
        $intendedMessage = session('intendedMessage');

        $myOpenCoachConversationRequest = PrivateMessage::myOpenCoachConversationRequest()->first();

        $selectedOption = $option;

        return view('cooperation.conversation-requests.edit', compact('myOpenCoachConversationRequest', 'selectedOption', 'intendedMessage'));
    }

    public function update(ConversationRequest $request, Cooperation $cooperation)
    {

        $user = \Auth::user();
        $cooperationId = session('cooperation');

        $message = $request->get('message', '');
        $action = $request->get('action', '');


        PrivateMessage::myOpenCoachConversationRequest()->update(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'title' => __('woningdossier.cooperation.conversation-requests.index.form.options.'.$action),
                'message' => $message,
                'to_cooperation_id' => $cooperationId,
                'from_user_id' => $user->id,
                'status' => PrivateMessage::STATUS_IN_CONSIDERATION,
                'request_type' => $action
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

        $selectedOption = __('woningdossier.cooperation.conversation-requests.edit.form.'.$action);

        $myOpenCoachConversationRequest = PrivateMessage::myOpenCoachConversationRequest()->first();

        if ($action == PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION && PrivateMessage::hasUserResponseToCoachConversationRequest() == false && $myOpenCoachConversationRequest != null) {
            return redirect()
                ->route('cooperation.conversation-requests.edit', ['cooperation' => $cooperation, 'option' => PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION])
                ->with('warning', __('woningdossier.cooperation.conversation-requests.update.warning', ['request_type' => $selectedOption]))
                ->with('intendedMessage', $message);
        }

        $user = \Auth::user();
        $cooperationId = session('cooperation');



        PrivateMessage::create(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'title' => __('woningdossier.cooperation.conversation-requests.index.form.options.'.$action),
                'message' => $message,
                'to_cooperation_id' => $cooperationId,
                'from_user_id' => $user->id,
                'status' => PrivateMessage::STATUS_IN_CONSIDERATION,
                'request_type' => $action
            ]
        );

        $cooperation = Cooperation::find($cooperationId);

        return redirect()->back()->with('success', __('woningdossier.cooperation.conversation-requests.store.success', ['url' => route('cooperation.my-account.index', ['cooperation' => $cooperation->slug])]));
    }

}
