<?php

namespace App\Policies;

use App\Helpers\Conditions\ConditionEvaluator;
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

        return ConditionEvaluator::init()
                                 ->building($building)
                                 ->inputSource($masterInputSource)
                                 ->evaluate($subStep->conditions ?? []);

//        if (!empty($subStep->conditions)) {
//            // we will collect the answers, this way we can query on the collection with the $conditions array.
//            $answers = collect();
//            $conditions = $subStep->conditions;
//
//            foreach ($conditions as $condition) {
//                $toolQuestion = ToolQuestion::findByShort($condition['column']);
//                // in case of checkbox $answer is array
//                // else plain value
//                $answer = $building->getAnswer($masterInputSource, $toolQuestion);
//
//                // so the checkbox-icon type returns a array of answers, we we will just check it here to prevent collection magic
//                if ($toolQuestion->toolQuestionType->short == 'checkbox-icon') {
//                    if (in_array($condition['value'], $answer)) {
//                        $answer = null;
//                    }
//                } else {
//                    $answers->push([$condition['column'] => $answer]);
//                }
//
//                //dump($answers);
//                // first check if the user actually gave an answer, which is mandatory but better to double check
//                if ($answers->filter()->isNotEmpty()) {
//                    foreach ($conditions as $condition) {
//                        // when no operator is given we have to check if the answer contains something
//                        $answers = $answers->where($condition['column'], $condition['operator'], $condition['value']);
//                    }
//                    // all answers have been filtered out based on the conditions, so we cant show the sub step
//                    if ($answers->isEmpty()) {
//                        return false;
//                    }
//                }
//            }
//            return true;
//        }
    }
}
