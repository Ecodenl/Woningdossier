<?php

namespace App\Helpers\Models;

use App\Models\Cooperation;
use App\Services\UserService;

class CooperationHelper
{
    public static function destroyCooperation(Cooperation $cooperation): void
    {
        $exampleBuildings = $cooperation->exampleBuildings;

        foreach ($exampleBuildings as $exampleBuilding) {
            $exampleBuilding->contents()->delete();
        }

        $cooperation->exampleBuildings()->delete();

        $users = $cooperation->users()->withoutGlobalScopes()->get();
        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            UserService::deleteUser($user, true);
        }

        $cooperation->delete();
    }
}
