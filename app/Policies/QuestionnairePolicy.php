<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionnairePolicy
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

    /**
     * Check if the user is permitted to edit the questionnaire.
     *
     * @param User          $user
     * @param Questionnaire $questionnaire
     *
     * @return bool
     */
    public function edit(User $user, Questionnaire $questionnaire)
    {
        // get the current cooperation
        $currentCooperation = HoomdossierSession::getCooperation(true);

        // check if the cooperation from the requested questionnaire is the same as the cooperation from the authenticated user
        if ($questionnaire->cooperation instanceof $currentCooperation) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user is permitted to set the active status of a questionnaire.
     *
     * @param User          $user
     * @param Questionnaire $questionnaire
     *
     * @return bool
     */
    public function setActiveStatus(User $user, Questionnaire $questionnaire)
    {
        // same logic (for now)
        return $this->edit($user, $questionnaire);
    }

    /**
     * Check if the user is permitted to create a new questionnaire.
     *
     * @param User $user
     *
     * @return bool
     */
    public function store(User $user)
    {
        $userCooperations = $user->cooperations()->get();
        $currentCooperation = HoomdossierSession::getCooperation(true);

        // if the user has the role coordinator and the cooperations from the user has the current cooperation authorize him.
        if ($user->hasRole('coordinator') && $userCooperations->contains($currentCooperation)) {
            return true;
        }

        return false;
    }

    public function update(User $user, Questionnaire $questionnaire)
    {
        return $this->edit($user, $questionnaire);
    }

    public function delete(User $user, Questionnaire $questionnaire)
    {
        $userCooperations = $user->cooperations()->get();
        $currentCooperation = HoomdossierSession::getCooperation(true);

        // check if the user has the coordinator role and check the current cooperation
        if ($user->hasRole('coordinator') && $userCooperations->contains($currentCooperation)) {
            // and check if the questionnaire from the question has a relation with the cooperation
            $cooperationFromQuestionnaire = $questionnaire->cooperation;

            if ($cooperationFromQuestionnaire == $currentCooperation) {
                return true;
            }
        }

        return false;
    }
}
