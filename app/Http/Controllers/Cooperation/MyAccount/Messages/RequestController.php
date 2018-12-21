<?php

namespace App\Http\Controllers\Cooperation\MyAccount\Messages;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PrivateMessage;

class RequestController extends Controller
{
    public function index()
    {
        $conversationRequests = PrivateMessage::myConversationRequest()->get();

        return view('cooperation.my-account.messages.requests.index', compact('conversationRequests'));
    }

    public function edit(Cooperation $cooperation, $requestMessageId)
    {
        $conversationRequest = PrivateMessage::myConversationRequest()->find($requestMessageId);

        return view('cooperation.my-account.messages.requests.edit', compact('conversationRequest'));
    }

    public function update(Request $request, Cooperation $cooperation, $requestMessageId)
    {

        $conversationRequest = PrivateMessage::myConversationRequest()->find($requestMessageId);

        $message = $request->get('message', '');

        $user = \Auth::user();
        $cooperationId = \Session::get('cooperation');

        $conversationRequest->update(
            [
                'message' => $message,
                'to_cooperation_id' => $cooperationId,
                'from_user_id' => $user->id,
                'status' => PrivateMessage::STATUS_IN_CONSIDERATION
            ]
        );

        return redirect()->route('cooperation.my-account.messages.requests.index')->with('success', __('woningdossier.cooperation.my-account.messages.requests.update.success', ['url' => route('cooperation.my-account.messages.requests.index')]));
    }
}
