<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function index()
    {
        $buildingPermissions = BuildingPermission::where('building_id', HoomdossierSession::getBuilding())->get();

        $lastKnownConversationRequest = PrivateMessage::conversationRequest(HoomdossierSession::getBuilding())->forMyCooperation()->get()->last();

        return view('cooperation.my-account.access.index', compact('buildingPermissions', 'lastKnownConversationRequest'));
    }

    public function allowAccess(Request $request)
    {
        $lastKnownConversationRequest = PrivateMessage::conversationRequest(HoomdossierSession::getBuilding())
            ->forMyCooperation()->get()->last();
        if ($request->has('allow_access')) {

            $lastKnownConversationRequest->allow_access = true;
            $lastKnownConversationRequest->save();
        } else {
            $lastKnownConversationRequest->allow_access = false;
            $lastKnownConversationRequest->save();
        }

        return redirect()->back();
    }
}
