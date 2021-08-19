<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Models\Account;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubStepPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function show(Account $account, SubStep $subStep)
    {
        $building = HoomdossierSession::getBuilding(true);
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        if (!empty($subStep->conditions)) {
            // we will collect the answers, this way we can query on the collection with the $conditions array.
            $answers = collect();
            $conditions = $subStep->conditions;
            foreach ($conditions as $condition) {
                $toolQuestion = ToolQuestion::findByShort($condition['column']);
//                dd($building->getAnswers($masterInputSource, $toolQuestion));
                // set the answers inside the collection
                $answers->push([$condition['column'] => $building->getAnswers($masterInputSource, $toolQuestion)]);
            }

            foreach ($answers as $answer) {
                foreach ($conditions as $condition) {
                    // now get the given answers for the given question
                    $givenAnswers = $answer[$condition['column']];
                    // so now we have to collect it, because we cant use operators stored in var's for a if.. (without eval)
                    collect();
                    dd($givenAnswers);
                }
            }
            // first check if the user actually gave an answer, which is mandatory but better to double check
            if ($answers->filter()->isNotEmpty()) {
                foreach ($conditions as $condition) {
                    $answers = $answers->where($condition['column'], $condition['operator'], $condition['value']);
                }
                // all answers have been filtered out based on the conditions, so we cant show the sub step
                if ($answers->isEmpty()) {
                    return false;
                }
            }
        }
        return true;
    }
}
