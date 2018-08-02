<?php

namespace App\Http\Controllers\Cooperation;

use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachConversationRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $privateMessage = PrivateMessage::myCoachConversationRequest()->first();

        return view('cooperation.coach-conversation.index', compact('privateMessage'));
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
        $cooperationId = \Session::get('cooperation');

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
                ]
            );
        }


        $cooperation = Cooperation::find($cooperationId);

        return redirect()->back()->with('success', __('woningdossier.cooperation.coach-conversation-request.store.success', ['url' => route('cooperation.my-account.index', ['cooperation' => $cooperation->slug])]));
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
