<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Requests\Cooperation\Admin\Coach\ConnectToResidentRequest;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConnectToResidentController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->getResidents()->get();

        return view('cooperation.admin.coach.connect-to-resident.index', compact('cooperation', 'users'));
    }

    public function create(Cooperation $cooperation, $userId)
    {
        $receiver = $cooperation->getResidents()->find($userId);


        if ($receiver instanceof User) {
            return view('cooperation.admin.coach.connect-to-resident.create', compact('cooperation', 'receiver', 'typeId'));
        }

        return redirect()->route('cooperation.admin.coach.connect-to-resident.index');
    }

    public function store(Cooperation $cooperation, ConnectToResidentRequest $request)
    {
        $requestType = $request->get('conversation-request-type', '');
        $message = $request->get('message', '');
        $receiverId = $request->get('receiver_id', '');

        $title = __('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.options.'.$requestType);

        // get the open request from the resident
        $residentRequest = PrivateMessage::where('from_user_id', $receiverId)
            ->where('to_cooperation_id', $cooperation->id)
            ->where('request_type', $requestType)
            ->where('status', PrivateMessage::STATUS_IN_CONSIDERATION)
            ->first();

        // if the there is no record found, don't send the resident a message cause he does not want any help
        // instead redirect the coach with a message
        if (!$residentRequest instanceof PrivateMessage) {
            return redirect()
                ->route('cooperation.admin.coach.connect-to-resident.index')
                ->with('warning', __('woningdossier.cooperation.admin.coach.connect-to-resident.store.warning'));
        }

        // then we update it and set it linked to coach
        $residentRequest->update(
            [
                'status' => PrivateMessage::STATUS_LINKED_TO_COACH
            ]
        );

        // we create a new message to send to the resident
        $newMessage = PrivateMessage::create(
            [
                'title' => $title,
                'status' => PrivateMessage::STATUS_IN_CONSIDERATION,
                'request_type' => $requestType,
                'message' => $message,
                'from_user_id' => \Auth::id(),
                'to_user_id' => $receiverId,
            ]
        );

        return redirect()->route('cooperation.admin.coach.messages.edit', ['messageId' => $newMessage->id]);
    }
}
