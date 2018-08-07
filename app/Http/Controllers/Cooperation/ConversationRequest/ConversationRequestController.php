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
        $privateMessage = PrivateMessage::myCoachConversationRequest()->first();

        $selectedOption = $option;

        return view('cooperation.conversation-requests.index', compact('privateMessage', 'selectedOption'));
    }

    public function edit(Cooperation $cooperation, $m)
    {

    }


    /**
     * Save the conversation request for whatever the conversation request may be
     *
     * @param ConversationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ConversationRequest $request)
    {

        $user = \Auth::user();
        $cooperationId = session('cooperation');

        $message = $request->get('message', '');
        $action = $request->get('action', '');


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
