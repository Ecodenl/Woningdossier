<?php

namespace App\Http\Controllers\Cooperation\MyAccount\Messages;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        return redirect()->route('cooperation.my-account.messages.index');
    }

    public function edit(Cooperation $cooperation, $requestMessageId)
    {
        return redirect()->route('cooperation.my-account.messages.index');
    }

    public function update(Request $request, Cooperation $cooperation, $requestMessageId)
    {
        $request->validate([
            'message' => 'required',
        ]);
        $conversationRequest = PrivateMessage::find($requestMessageId);

        $message = $request->get('message', $conversationRequest->message);
        $cooperationId = HoomdossierSession::getCooperation();
        $allowAccess = empty($request->get('allow_access', '')) ? false : true;

        $conversationRequest->update(
            [
                'message' => $message,
                'to_cooperation_id' => $cooperationId,
                'allow_access' => $allowAccess,
            ]
        );

        return redirect()->route('cooperation.my-account.messages.requests.index')->with('success', __('woningdossier.cooperation.my-account.messages.requests.update.success', ['url' => route('cooperation.my-account.messages.requests.index')]));
    }
}
