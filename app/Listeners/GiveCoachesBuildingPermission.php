<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;

class GiveCoachesBuildingPermission
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $building = $event->building;

        // check if there are any conversation requests.
        // if so, set the allow access to true, and give the connected coaches access.
        if (PrivateMessage::conversationRequestByBuildingId($building->id)->exists()) {
            // update all messages with allow_access to true.
            PrivateMessage::conversationRequestByBuildingId($building->id)
                ->update(['allow_access' => true]);

            // todo: allowing access to a building became mandatory upon registering.
            // todo: the code beneath here will become redundant when we decide to make it permanent.

            // get all the coaches that are currently connected to the building
            $coachesWithAccessToResidentBuildingStatuses = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);

            // we give the coaches that have "permission" to talk to a resident the permissions to access the building from the resident.
            foreach ($coachesWithAccessToResidentBuildingStatuses as $coachWithAccessToResidentBuildingStatus) {
                BuildingPermission::create([
                    'user_id' => $coachWithAccessToResidentBuildingStatus->coach_id,
                    'building_id' => $coachWithAccessToResidentBuildingStatus->building_id,
                ]);
            }
        } else {
            $cooperation = HoomdossierSession::getCooperation(true);

            // withoutEvents is added in laravel 7, for now this will do.
            // https://github.com/laravel/framework/blob/7.x/src/Illuminate/Database/Eloquent/Concerns/HasEvents.php#L399
            $privateMessage = PrivateMessage::class;
            $privateMessage::unsetEventDispatcher();

            // create the initial message
            $privateMessage = $privateMessage::create([
                'is_public' => true,
                'from_cooperation_id' => $cooperation->id,
                'to_cooperation_id' => $cooperation->id,
                'from_user' => $cooperation->name,
                'message' => 'Welkom bij het Hoomdossier, hier kunt u chatten met de coöperatie.',
                'building_id' => $building->id,
            ]);

            // what should we set the building status to ?
            $building->setStatus('pending');

            // give the user a unread message.
            PrivateMessageView::create([
                'input_source_id' => InputSource::findByShort(InputSource::RESIDENT_SHORT)->id,
                'private_message_id' => $privateMessage->id,
                'user_id' => $building->user->id,
            ]);
        }
    }
}
