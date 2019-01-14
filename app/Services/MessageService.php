<?php

namespace App\Services;

use App\Models\Building;
use App\Models\PrivateMessage;
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
        $buildingId = $request->get('building_id', '');

        $building = Building::find($buildingId);

        // if the building exist create a message
        if ($building instanceof Building) {
            PrivateMessage::create(
                [
                    'from_user' => \Auth::user()->getFullName(),
                    'message' => $message,
                    'building_id' => $buildingId,
                ]
            );
        }

        return true;
    }
}
