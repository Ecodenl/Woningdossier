<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\Role;
use Illuminate\Http\Request;

class MessageService
{
    /**
     * Create a new message between a user and user.
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function create(Request $request)
    {
        $message = $request->get('message', '');

        $isPublic = $request->get('is_public', true);
        $buildingId = $request->get('building_id', '');

        // if the is public is set to false
        // and the user current role is resident, then something isnt right.
        // since a resident cant access the private group chat
        if (! $isPublic && 'resident' == Role::find(HoomdossierSession::getRole())->name) {
            return redirect()->back();
        }

        $building = Building::find($buildingId);

        // if the building exist create a message
        if ($building instanceof Building) {
            $privateMessageData = [
                'is_public' => $isPublic,
                'to_cooperation_id' => HoomdossierSession::getCooperation(),
                'from_user' => \Auth::user()->getFullName(),
                'from_user_id' => \Auth::id(),
                'message' => $message,
                'building_id' => $buildingId,
            ];

            // users that have the role coordinator and cooperation admin dont talk from themself but from a cooperation
            if (\Auth::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
                $privateMessageData['from_cooperation_id'] = HoomdossierSession::getCooperation();
                $privateMessageData['from_user'] = Cooperation::find(HoomdossierSession::getCooperation())->name;
            }

            PrivateMessage::create(
                $privateMessageData
            );
        }

        return true;
    }
}
