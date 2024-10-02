<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Media;
use App\Services\BuildingCoachStatusService;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    public function before(Account $user, $ability, $media, InputSource $inputSource, Building $building)
    {
        // If user owns the building he can do everything.
        if ($building->id === $user->user()->building?->id) {
            return true;
        } elseif ($inputSource->short === InputSource::COACH_SHORT) {
            // A coach is not allowed to do anything if he isn't coupled.
            // Get the buildings the user is connected to.
            $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser($user->user());

            // Check if the current building is in that collection.
            if (! $connectedBuildingsForUser->contains('building_id', $building->id)) {
                return false;
            }
        }
    }

    /**
     * Determine whether the user can view any media.
     */
    public function viewAny(Account $user, InputSource $inputSource, Building $building, ?string $tag = null): bool
    {
        // Media can be viewed if it's either the user's own building, or the user
        // belongs to the cooperation (coach belongs to cooperation).

        return ($this->isCooperation($inputSource) || HoomdossierSession::isUserObserving())
            && $tag !== MediaHelper::BUILDING_IMAGE;
    }

    /**
     * Determine whether the user can view the media.
     */
    public function view(Account $user, Media $media, InputSource $inputSource, Building $building): bool
    {
        return ($this->isCooperation($inputSource) || HoomdossierSession::isUserObserving())
            && data_get($media->custom_properties, 'share_with_cooperation');
    }

    /**
     * Determine whether the user can create media.
     */
    public function create(Account $user, InputSource $inputSource, Building $building, ?string $tag = null): bool
    {
        // Only a coach is allowed to upload a building image as of now
        return ! HoomdossierSession::isUserObserving() && (
                ($tag !== MediaHelper::BUILDING_IMAGE && $this->isCooperation($inputSource)) ||
                $inputSource->short === InputSource::COACH_SHORT
            );
    }

    /**
     * Determine whether the user can update the media.
     */
    public function update(Account $user, Media $media, InputSource $inputSource, Building $building): bool
    {
        return $this->isCooperation($inputSource) && ! HoomdossierSession::isUserObserving();
    }

    /**
     * Determine whether the user can delete the media.
     */
    public function delete(Account $user, Media $media, InputSource $inputSource, Building $building, ?string $tag = null): bool
    {
        return data_get($media->custom_properties, 'share_with_cooperation')
            && ! HoomdossierSession::isUserObserving() && (
                ($tag !== MediaHelper::BUILDING_IMAGE && $this->isCooperation($inputSource)) ||
                $inputSource->short === InputSource::COACH_SHORT
            );
    }

    public function shareWithCooperation(Account $user, Media $media, InputSource $inputSource, Building $building): bool
    {
        return $building->id === $user->user()->building?->id;
    }

    private function isCooperation(InputSource $inputSource): bool
    {
        return in_array($inputSource->short, [InputSource::COACH_SHORT, InputSource::COOPERATION_SHORT]);
    }
}
