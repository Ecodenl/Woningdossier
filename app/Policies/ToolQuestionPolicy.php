<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\ToolQuestion;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class ToolQuestionPolicy
{
    use HandlesAuthorization;

    public function answer(Account $account, ToolQuestion $toolQuestion)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);

        if (HoomdossierSession::isUserObserving()) {
            return false;
        }

        return is_null($toolQuestion->forSpecificInputSource) || $currentInputSource->short === $toolQuestion->forSpecificInputSource->short;
    }
}
