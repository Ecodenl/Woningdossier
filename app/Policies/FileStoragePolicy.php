<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\User;
use App\Services\SmartTwin\SmartTwinFileTypes;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileStoragePolicy
{
    use HandlesAuthorization;

    public function download(Account $account, FileStorage $fileStorage, ?Building $building = null)
    {
        $user = $account->user();

        // The SmartTwin artifacts are debug material, and are downloaded through the dedicated
        // super admin route instead. They are never available here: not to the cooperation, and
        // not to the resident whose building they describe.
        if ($this->isSmartTwinArtifact($fileStorage)) {
            return false;
        }

        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_SUPER_ADMIN]) && $fileStorage->fileType->short === 'example-building-overview') {
            return true;
        }

        // some other logic for resident wil come in the near future.
        if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COOPERATION_ADMIN, RoleHelper::ROLE_COORDINATOR]) && $fileStorage->cooperation_id == HoomdossierSession::getCooperation()) {
            return true;
        }

        $inputSource = HoomdossierSession::getInputSource(true);

        if ($building instanceof Building && $building->isOwnerOfFileStorage($inputSource, $fileStorage)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can store a file.
     */
    public function store(Account $account, FileStorage $fileStorage, FileType $fileType): bool
    {
        $user = $account->user();
        switch ($fileType->short) {
            case SmartTwinFileTypes::ADVICE_RAW:
            case SmartTwinFileTypes::MAPPING_REPORT:
                // Written by the SmartTwin pipeline itself, never generated on request.
                return false;
            case 'pdf-report':
                if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COACH, RoleHelper::ROLE_RESIDENT, RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COOPERATION_ADMIN])) {
                    return true;
                }
                break;
            case 'example-building-overview':
                return $user->hasRoleAndIsCurrentRole(RoleHelper::ROLE_SUPER_ADMIN);
            default:
                // for now default, in the future more cases may be specified.
                if ($user->hasRoleAndIsCurrentRole([RoleHelper::ROLE_COORDINATOR, RoleHelper::ROLE_COOPERATION_ADMIN])) {
                    return true;
                }
                break;
        }

        return false;
    }

    private function isSmartTwinArtifact(FileStorage $fileStorage): bool
    {
        return in_array($fileStorage->fileType->short, SmartTwinFileTypes::all(), true);
    }
}
