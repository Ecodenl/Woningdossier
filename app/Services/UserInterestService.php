<?php

namespace App\Services;

use App\Models\InputSource;
use App\Models\Interest;
use App\Models\User;
use App\Models\UserInterest;
use Illuminate\Support\Facades\Log;

class UserInterestService
{
    /**
     * Method to save a interest for a specific type and id.
     *
     * @param $interestedInType
     */
    public static function save(User $user, InputSource $inputSource, $interestedInType, int $interestedInId, int $interestId)
    {
        Log::debug('Saving the user interest on the');
        Log::debug("interest_in_id {$interestedInId}");
        Log::debug("interest_in_type {$interestedInType}");
        Log::debug("interest_id {$interestId}");

        UserInterest::withoutGlobalScopes()->updateOrCreate(
            [
                'user_id' => $user->id,
                'input_source_id' => $inputSource->id,
                'interested_in_type' => $interestedInType,
                'interested_in_id' => $interestedInId,
            ],
            [
                'interest_id' => $interestId,
            ]
        );
    }
}
