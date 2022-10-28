<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Building;
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
    public function viewAny(Account $user, InputSource $inputSource, Building $building): bool
    {
        // Media can be viewed if it's either the user's own building, the user is a coach, or the user
        // belongs to the cooperation.

        return $this->ownsBuilding($user, $building)
            || $this->isCooperation($inputSource);
    }

    /**
     * Determine whether the user can view the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function view(Account $user, Media $media, InputSource $inputSource, Building $building): bool
    {
        return $this->ownsBuilding($user, $building)
            || ($this->isCooperation($inputSource) && data_get($media->custom_properties, 'share_with_cooperation'));
    }

    /**
     * Determine whether the user can create media.
     *
     * @param \App\Models\Account $user
     *
     * @return mixed
     */
    public function create(Account $user, InputSource $inputSource, Building $building): bool
    {
        return $this->ownsBuilding($user, $building) || $this->isCooperation($inputSource);
    }

    /**
     * Determine whether the user can update the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function update(Account $user, Media $media, InputSource $inputSource, Building $building): bool
    {
        return $this->ownsBuilding($user, $building) || $this->isCooperation($inputSource);
    }

    /**
     * Determine whether the user can delete the media.
     *
     * @param \App\Models\Account $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function delete(Account $user, Media $media, InputSource $inputSource, Building $building): bool
    {
        return $this->ownsBuilding($user, $building)
            || ($this->isCooperation($inputSource) && data_get($media->custom_properties, 'share_with_cooperation'));
    }

    public function shareWithCooperation(Account $user, Media $media, InputSource $inputSource, Building $building): bool
    {
        return $this->ownsBuilding($user, $building);
    }

    private function ownsBuilding(Account $user, Building $building): bool
    {
        return $building->id === $user->user()->building->id;
    }

    private function isCooperation(InputSource $inputSource): bool
    {
        return in_array($inputSource->short, [InputSource::COACH_SHORT, InputSource::COOPERATION_SHORT]);
    }
}
