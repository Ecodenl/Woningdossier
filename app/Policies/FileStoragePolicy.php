<?php

namespace App\Policies;

use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileStoragePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the fileStorage.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\FileStorage $fileStorage
     *
     * @return mixed
     */
    public function view(User $user, FileStorage $fileStorage)
    {
    }

    /**
     * Determine whether the user can create fileStorages.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can store a file.
     *
     * @param User        $user
     * @param FileStorage $fileStorage
     * @param FileType    $fileType
     *
     * @return bool
     */
    public function store(User $user, FileStorage $fileStorage, FileType $fileType)
    {
        switch ($fileType->short) {
            case 'pdf-report':
                if ($user->hasRoleAndIsCurrentRole(['coach', 'resident'])) {
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
     * @param \App\Models\User        $user
     * @param \App\Models\FileStorage $fileStorage
     *
     * @return mixed
     */
    public function update(User $user, FileStorage $fileStorage)
    {
    }

    /**
     * Determine whether the user can delete the fileStorage.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\FileStorage $fileStorage
     *
     * @return mixed
     */
    public function delete(User $user, FileStorage $fileStorage)
    {
    }
}
