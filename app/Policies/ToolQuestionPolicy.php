<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\ToolQuestion;
use Illuminate\Auth\Access\HandlesAuthorization;

class ToolQuestionPolicy
{
    use HandlesAuthorization;

    public function answer(Account $account, ToolQuestion $toolQuestion)
    {
        $currentInputSource = HoomdossierSession::getInputSource(true);

        return is_null($toolQuestion->forSpecificInputSource) || $currentInputSource->short === $toolQuestion->forSpecificInputSource->short;
    }
}
