<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileStoragePolicy
{
    use HandlesAuthorization;

    public function download(Account $account, FileStorage $fileStorage, Building $building = null)
    {
        $user = $account->user();
        // some other logic for resident wil come in the near future.
        if ($user->hasRoleAndIsCurrentRole(['cooperation-admin', 'coordinator']) && $fileStorage->cooperation_id == HoomdossierSession::getCooperation()) {
            return true;
        }

        $inputSource = HoomdossierSession::getInputSource(true);

        if ($building instanceof Building && $building->isOwnerOfFileStorage($inputSource, $fileStorage)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the fileStorage.
     *
     * @return mixed
     */
    public function view(Account $account, FileStorage $fileStorage)
    {
    }

    /**
     * Determine whether the user can create fileStorages.
     *
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can store a file.
     *
     * @return bool
     */
    public function store(Account $account, FileStorage $fileStorage, FileType $fileType)
    {
        $user = $account->user();
        switch ($fileType->short) {
            case 'pdf-report':
                if ($user->hasRoleAndIsCurrentRole(['coach', 'resident', 'coordinator', 'cooperation-admin'])) {
                    return true;
                }
                break;
            default:
                // for now default, in the future more cases may be specified.
                if ($user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can update the fileStorage.
     *
     * @return mixed
     */
    public function update(Account $account, FileStorage $fileStorage)
    {
    }

    /**
     * Determine whether the user can delete the fileStorage.
     *
     * @return mixed
     */
    public function delete(Account $account, FileStorage $fileStorage)
    {
    }
}
