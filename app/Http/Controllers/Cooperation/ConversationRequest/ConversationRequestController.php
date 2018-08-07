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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (PrivateMessage::hasUserResponseToCoachConversationRequest()) {
            return redirect()->route('cooperation.my-account.messages.index');
        }

        $privateMessage = PrivateMessage::myCoachConversationRequest()->first();

        return view('cooperation.conversation-requests.index', compact('privateMessage'));
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
    public function store(ConversationRequest $request)
    {


        $message = $request->get('message', '');
        $action = $request->get('action', '');


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
