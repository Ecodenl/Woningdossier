<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SuccessFullLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param $event
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($event)
    {
        /** @var Account $account */
        $account = $event->user;

        // You may ask yourself "But John Doe, why would you check if the user is set when this is a SuccessFullLoginListener"
        // This can happen in the case where a user gets deleted by a cooperation (and is attached to multiple cooperations)
        // so Laravel will try to login the user, because of the cookie / session
        // than it will find a account (user) and log him in, but the account has no user for "this" cooperation so it will fail
        // and thus we will check if the account has a user, and if not we will log him out and the cookie with its session are gone foreeveeerrr
        $this->ensureCooperationIsSet();

        $user = $account->user();

        if (! $user instanceof User) {
            \Illuminate\Support\Facades\Log::debug('Account has no user, logging out and exiting.');
            Auth::logout();
            $account->setRememberToken(null);
            $account->save();
            header('Location: '.route('cooperation.welcome'));
            exit;
        }

        // cooperation is set, so we can safely retrieve the user from the account.
        // get the first building from the user
        $building = $user->building;
        // just get the first available role from the user
        $role = $user->roles->first();

        // if the user for some odd reason has no role attached, attach the resident rol to him.
        if (! $role instanceof Role) {
            $residentRole = Role::findByName('resident');
            $user->assignRole($residentRole);
            $role = $residentRole;
        }

        // get the input source
        $inputSource = $role->inputSource;

        // if there is only one role set for the user, and that role does not have an input source we will set it to resident.
        if (! $role->inputSource instanceof InputSource) {
            $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        }

        // set the required sessions
        HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSource, $role);

        Log::create([
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'building_id' => $building->id,
            'message' => __('woningdossier.log-messages.logged-in', [
                'full_name' => $user->getFullName(),
            ]),
        ]);
    }

    /**
     * Method to ensure the cooperation is set in the session.
     *
     * @return void
     */
    private function ensureCooperationIsSet()
    {
        /** @var Cooperation|string $cooperation */
        $cooperation = request()->route('cooperation');

        // if the user logs in through the remember the router is not booted yet.
        if (! $cooperation instanceof Cooperation) {
            $cooperation = Cooperation::where('slug', $cooperation)->first();
        }

        if ($cooperation instanceof Cooperation) {
            HoomdossierSession::setCooperation($cooperation);
        }

        // this boots before the router, so we check if the request contains deleted cooperations
        // and log them out and forget the remember me cookie
        if (in_array(request()->route('cooperation'), ['hnwr'])) {
            Auth::logout();
            exit;
        }
    }
}
