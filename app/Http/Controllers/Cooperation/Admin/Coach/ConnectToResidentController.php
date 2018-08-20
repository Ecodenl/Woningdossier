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

        // a user will not be found if its not a resident of the current cooperation
        if ($receiver instanceof \stdClass) {
            return view('cooperation.admin.coach.connect-to-resident.create', compact('cooperation', 'receiver', 'typeId'));
        }

        return redirect(back());
    }

    public function store(Cooperation $cooperation, ConnectToResidentRequest $request)
    {
        $requestType = $request->get('conversation-request-type', '');
        $message = $request->get('message', '');
        $receiverId = $request->get('receiver_id', '');

        $title = __('woningdossier.cooperation.admin.coach.connect-to-resident.create.form.options.'.$requestType);

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
