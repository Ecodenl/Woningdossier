<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Media;
use App\Services\BuildingCoachStatusService;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    public function before(?Account $user, string $ability, string|Media $media, ?InputSource $inputSource, null|Building|Cooperation $mediable = null, ?string $tag = null)
    {
        // Order of given parameters is consistent. Mediable and tag might not be provided. Inputsource can be nullable
        // in the case of not being logged in (e.g. login page, register page). In that case, the account is also
        // null.

        if ($media instanceof Media) {
            // If user owns the media he can do everything.
            if ($user instanceof Account && $media->ownedBy($user->user())) {
                return true;
            } elseif ($inputSource?->short === InputSource::COACH_SHORT && $mediable instanceof Building) {
                // A coach is not allowed to do anything if he isn't coupled.
                // Get the buildings the user is connected to.
                $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser($user->user());

                // Check if the current building is in that collection.
                if (! $connectedBuildingsForUser->contains('building_id', $mediable->id)) {
                    return false;
                }
            } elseif ($ability === 'view' && $media->cooperations()->exists()) {
                // The cooperation media is publicly viewable. Since media is only coupled to one model, we don't
                // need to check further.
                return true;
            }
        } elseif ($mediable instanceof Cooperation) {
            // The cooperation media is publicly viewable.
            // A super admin can manage cooperations and can upload media. Otherwise, only cooperation admins
            // belonging to the cooperation can manage.
            return $ability === 'viewAny' || ($user instanceof Account && $this->canManageCooperationMedia($user, $mediable));
        }
    }

    /**
     * Determine whether the user can view any media.
     */
    public function viewAny(Account $user, InputSource $inputSource, Building|Cooperation $mediable): bool
    {
        // Before hook handles:
        // - $mediable cooperation case
        // User can obviously see his own media. If it's a cooperation source (or observing, which is resident source),
        // then the user can see the building's media if allow access is true.
        return $mediable instanceof Building
            && (
                $mediable->user_id === $user->user()->id
                || (($this->isCooperationSource($inputSource) || HoomdossierSession::isUserObserving()) && $mediable->user->allowedAccess())
            );
    }

    /**
     * Determine whether the user can view the media.
     */
    public function view(Account $user, Media $media, InputSource $inputSource): bool
    {
        // Before hook handles:
        // - User owns media
        // - Cooperation media
        // - Coach may not see media if not coupled
        // Building media can be viewed if cooperation source and the media itself is shared with the cooperation.
        return ($this->isCooperationSource($inputSource) || HoomdossierSession::isUserObserving())
            && data_get($media->custom_properties, 'share_with_cooperation');
    }

    /**
     * Determine whether the user can create media.
     */
    public function create(Account $user, InputSource $inputSource, Building|Cooperation $mediable, ?string $tag = null): bool
    {
        // Before hook handles:
        // - Cooperation media
        // User may not be observing. Only a coach can upload a building image.
        // A cooperation source may upload for a building if access given. A user may always upload.
        if (HoomdossierSession::isUserObserving()) {
            return false;
        }

        if ($tag === MediaHelper::BUILDING_IMAGE) {
            return $inputSource->short === InputSource::COACH_SHORT;
        }

        return ! $this->isCooperationSource($inputSource) || $mediable->user->allowedAccess();
    }

    /**
     * Determine whether the user can update the media.
     */
    public function update(Account $user, Media $media, InputSource $inputSource): bool
    {
        // Before hook handles:
        // - User owns media
        if ($media->cooperations()->exists()) {
            // Cooperation related media

            // A super admin can manage cooperations and can upload media. Otherwise, only cooperation admins
            // belonging to the cooperation can manage.
            $cooperation = $media->cooperations()->first();
            return $this->canManageCooperationMedia($user, $cooperation);
        } else {
            // Building related media

            // May not be observing, and should be a cooperation source. The media must be shared with the cooperation
            return ! HoomdossierSession::isUserObserving()
                && data_get($media->custom_properties, 'share_with_cooperation')
                && $this->isCooperationSource($inputSource);
        }
    }

    /**
     * Determine whether the user can delete the media.
     */
    public function delete(Account $user, Media $media, InputSource $inputSource): bool
    {
        // Before hook handles:
        // - User owns media
        if ($media->cooperations()->exists()) {
            // Cooperation related media

            // A super admin can manage cooperations and can upload media. Otherwise, only cooperation admins
            // belonging to the cooperation can manage.
            $cooperation = $media->cooperations()->first();
            return $this->canManageCooperationMedia($user, $cooperation);
        } else {
            // Building related media

            // May not be observing, and should be a cooperation source. The media must be shared with the cooperation.
            // Only a coach may manage the building image.
            return ! HoomdossierSession::isUserObserving()
                && data_get($media->custom_properties, 'share_with_cooperation')
                && (
                    ($media->tag === MediaHelper::BUILDING_IMAGE && $inputSource->short === InputSource::COACH_SHORT) ||
                    ($media->tag !== MediaHelper::BUILDING_IMAGE && $this->isCooperationSource($inputSource))
                );
        }
    }

    /**
     * Whether or not the current user can alter the 'share-with-cooperation' state.
     */
    public function shareWithCooperation(Account $user, Media $media, InputSource $inputSource): bool
    {
        // NOTE: This statement is basically useless since it's already captured in the before hook.
        return $media->ownedBy($user->user());
    }

    private function canManageCooperationMedia(Account $user, Cooperation $cooperation): bool
    {
        return $user->user()->hasRoleAndIsCurrentRole(RoleHelper::ROLE_SUPER_ADMIN) || (
            $user->user()->hasRoleAndIsCurrentRole(RoleHelper::ROLE_COOPERATION_ADMIN)
            && $user->user()->cooperation_id === $cooperation->id
        );
    }

    private function isCooperationSource(InputSource $inputSource): bool
    {
        return in_array($inputSource->short, [InputSource::COACH_SHORT, InputSource::COOPERATION_SHORT]);
    }
}
