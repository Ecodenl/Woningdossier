<?php

namespace App\Services\SmartTwin;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Services\SmartTwin\Api\UserRole;
use RuntimeException;

/**
 * Whether the woonplan may send the current account into SmartTwin: for its check, or for a new
 * package. Asked by the woonplan page and, once the woonplan is SmartTwin's, by the button under
 * its results, so both offer the same thing to the same people.
 */
final class SmartTwinHandOff
{
    /**
     * Whether we can send this account into SmartTwin at all.
     */
    public static function isOfferedFor(Building $building): bool
    {
        // Also covers a building with only the lite scan: SmartTwin's check is a quick scan, and
        // what it sends back has no place in a lite scan dossier.
        if (! Hoomdossier::smartTwinFillsWoonplanFor($building)) {
            return false;
        }

        // SmartTwin has a tool for a resident and one for a coach, and nothing for anyone else. An
        // account whose roles are only coordinator or cooperation-admin is skipped when SmartTwin
        // users are created (see SmartTwinEventSubscriber::dispatchForAccount), so it has no
        // SmartTwin user by design, and the hand-off would come back "unsupported role" anyway.
        $roleName = HoomdossierSession::getRole(true)?->name;

        if (! in_array($roleName, [RoleHelper::ROLE_RESIDENT, RoleHelper::ROLE_COACH], true)) {
            return false;
        }

        // An API client can be authenticated here too, and has no SmartTwin account to speak of.
        $account = Hoomdossier::account();

        if ($account instanceof Account && ! empty($account->smartTwinUserId())) {
            // An account gets one SmartTwin user, created for one role, and coach wins when an
            // account holds both (see SmartTwinEventSubscriber). So holding an id is not the same
            // as being able to use it in the role you are currently in: SmartTwin refuses a
            // quick-scan link for an Advisor account, and the other way round. Offering the button
            // in that case would be offering a round trip to an error message.
            $needed = $roleName === RoleHelper::ROLE_COACH ? UserRole::Advisor : UserRole::Resident;

            // Not reported: this is a known trade-off of one SmartTwin user per account, not a
            // fault in ours.
            return $account->smartTwinUserRole() === $needed;
        }

        // A resident or a coach is supposed to have one: it is handed out when the SmartTwin user is
        // created. Not having one means that never happened. Nothing the user can do about it and
        // nothing useful to show them, but it is not supposed to occur, so it is reported rather
        // than silently swallowed.
        report(new RuntimeException(sprintf(
            'Account %s (%s) has no SmartTwin user id, so the hand-off cannot be offered.',
            $account instanceof Account ? $account->id : 'unknown',
            $roleName,
        )));

        return false;
    }
}
