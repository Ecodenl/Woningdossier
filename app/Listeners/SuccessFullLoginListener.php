<?php

namespace App\Listeners;

use Illuminate\Http\RedirectResponse;
use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Log;
use App\Models\Role;
use App\Models\User;
use App\Services\DiscordNotifier;
use App\Services\Models\BuildingService;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\Auth;

class SuccessFullLoginListener
{
    protected BuildingService $buildingService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): RedirectResponse
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

            try {
                $route = route('cooperation.welcome');
            } catch (UrlGenerationException $e) {
                $coop = request()->route('cooperation');

                $label = is_string($coop) ? $coop : null;
                if ($coop instanceof Cooperation) {
                    $label = $coop->name;
                    $route = route('cooperation.welcome', ['cooperation' => $coop]);
                } else {
                    $route = route('index');
                }

                DiscordNotifier::init()
                    ->notify('No cooperation during invalid login? Cooperation: ' . $label);
            }

            header('Location: ' . $route);
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
        if (! $inputSource instanceof InputSource) {
            $inputSource = InputSource::resident();
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

        $this->buildingService->forBuilding($building)->forInputSource($inputSource)->performMunicipalityCheck();
    }

    /**
     * Method to ensure the cooperation is set in the session.
     */
    private function ensureCooperationIsSet(): void
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
