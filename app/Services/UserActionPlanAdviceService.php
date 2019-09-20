<?php

namespace App\Services;

use App\Models\InputSource;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;

class UserActionPlanAdviceService {

    /**
     * Method to return input sources that have an action plan advice, on a building
     *
     * @param  User $user
     * @param InputSource $exceptForInputSource
     * @return UserActionPlanAdvice[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public static function availableInputSourcesForActionPlan(User $user)
    {
        return UserActionPlanAdvice::withoutGlobalScope(GetValueScope::class)
            ->where('user_id', $user->id)
//            ->whereHas('inputSource', function ($query) use ($exceptForInputSource) {
//                $query->where('short', '!=', $exceptForInputSource->short);
//            })
            ->select('input_source_id')
            ->groupBy('input_source_id')
            ->get()
            ->map(function ($userActionPlanAdvice) {
                return $userActionPlanAdvice->inputSource;
            });
    }
}