<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\InputSource;
use App\Models\Media;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any media.
     *
     * @param \App\Models\Account $user
     *
     * @return mixed
     */
    public function viewAny(Account $user)
    {
        // Media can be viewed if it's either the user's own building, the user is a coach, or the user
        // belongs to the cooperation.
        $currentInputSource = HoomdossierSession::getInputSource(true);
        $currentBuilding = HoomdossierSession::getBuilding();

        return $currentBuilding === $user->user()->building->id
            || $currentInputSource->short === InputSource::COACH_SHORT
            || HoomdossierSession::isUserObserving();
    }

    /**
     * Determine whether the user can view the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function view(Account $user, Media $media)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);
        $currentBuilding = HoomdossierSession::getBuilding();

        return $currentBuilding === $user->user()->building->id
            || $currentInputSource->short === InputSource::COACH_SHORT
            || (HoomdossierSession::isUserObserving() && data_get($media->custom_properties, 'share_with_cooperation'));
    }

    /**
     * Determine whether the user can create media.
     *
     * @param \App\Models\Account $user
     *
     * @return mixed
     */
    public function create(Account $user)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);
        $currentBuilding = HoomdossierSession::getBuilding();

        return $currentBuilding === $user->user()->building->id
            || $currentInputSource->short === InputSource::COACH_SHORT;
    }

    /**
     * Determine whether the user can update the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function update(Account $user, Media $media)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);
        $currentBuilding = HoomdossierSession::getBuilding();

        return $currentBuilding === $user->user()->building->id
            || $currentInputSource->short === InputSource::COACH_SHORT;
    }

    /**
     * Determine whether the user can delete the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function delete(Account $user, Media $media)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);
        $currentBuilding = HoomdossierSession::getBuilding();

        return $currentBuilding === $user->user()->building->id
            || $currentInputSource->short === InputSource::COACH_SHORT;
    }

    /**
     * Determine whether the user can restore the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function restore(Account $user, Media $media)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function forceDelete(Account $user, Media $media)
    {
        //
    }
}
