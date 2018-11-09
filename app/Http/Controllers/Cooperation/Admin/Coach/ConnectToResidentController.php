<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\HoomdossierSession;
use App\Http\Requests\Cooperation\Admin\Coach\ConnectToResidentRequest;
use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConnectToResidentController extends Controller
{
    public function index(Cooperation $cooperation)
    {

        // the coach is allowed to talk to a resident if he has permission to that building.
        $users = \DB::table('building_permissions')
            ->where('building_permissions.user_id', \Auth::id())
            ->leftJoin('buildings', 'buildings.id', '=', 'building_permissions.building_id')
            ->leftJoin('users', 'users.id', '=', 'buildings.user_id')
            ->select('users.*')->get();


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
        $title = $request->get('title', '');
        $message = $request->get('message', '');
        $receiverId = $request->get('receiver_id', '');

        // Get the building from the receiver / resident
        $buildingFromUser = Building::where('user_id', $receiverId)->first();
        // If the coach does not have permission to this building redirect him.
        $buildingPermission = BuildingPermission::where('building_id', $buildingFromUser->id)->where('user_id', \Auth::id())->first();
        if (!$buildingPermission instanceof BuildingPermission) {
            return redirect()
                ->route('cooperation.admin.coach.connect-to-resident.index')
                ->with('warning', __('woningdossier.cooperation.admin.coach.connect-to-resident.store.warning'));
        }

        // we start the conversation between the resident and coach
        $newMessage = PrivateMessage::create(
            [
                'title' => $title,
                'message' => $message,
                'from_user_id' => \Auth::id(),
                'to_user_id' => $receiverId,
            ]
        );

        return redirect()->route('cooperation.admin.coach.messages.edit', ['messageId' => $newMessage->id]);
    }
}
