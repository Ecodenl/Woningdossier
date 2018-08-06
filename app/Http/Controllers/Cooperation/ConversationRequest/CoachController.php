<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (PrivateMessage::hasUserResponseToConversationRequest()) {
            return redirect()->route('cooperation.my-account.messages.index');
        }

        $privateMessage = PrivateMessage::myCoachConversationRequest()->first();

        return view('cooperation.conversation-requests.coach.index', compact('privateMessage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = $request->get('message', '');

        $user = Auth::user();
        $cooperationId = session('cooperation');

        $privateMessage = PrivateMessage::myCoachConversationRequest()->first();

        if (isset($privateMessage)) {
            $privateMessage->update(
                [
                    'message' => $message,
                ]
            );
        } else {
            PrivateMessage::create(
                [
                    'message' => $message,
                    'to_cooperation_id' => $cooperationId,
                    'from_user_id' => $user->id,
                    'status' => PrivateMessage::STATUS_IN_CONSIDERATION,
                    'request_type' => PrivateMessage::REQUEST_TYPE_COACH_CONVERSATION,
                ]
            );
        }


        $cooperation = Cooperation::find($cooperationId);

        return redirect()->back()->with('success', __('woningdossier.cooperation.conversation-request.store.success', ['url' => route('cooperation.my-account.index', ['cooperation' => $cooperation->slug])]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
