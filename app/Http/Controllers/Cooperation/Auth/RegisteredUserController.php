<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Models\Building;
use Illuminate\Http\JsonResponse;
use App\Events\Registered;
use App\Events\UserAllowedAccessToHisBuilding;
use App\Events\UserAssociatedWithOtherCooperation;
use App\Helpers\Models\CooperationSettingHelper;
use App\Models\Account;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterViewResponse;

class RegisteredUserController extends \Laravel\Fortify\Http\Controllers\RegisteredUserController
{
    /**
     * Show the registration view.
     *
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Laravel\Fortify\Contracts\RegisterViewResponse|mixed
     */
    public function index(Request $request)
    {
        $registerUrl = CooperationSettingHelper::getSettingValue(
            $request->route('cooperation'),
            CooperationSettingHelper::SHORT_REGISTER_URL
        );

        if (! empty($registerUrl)) {
            return redirect()->away($registerUrl);
        }

        return app(RegisterViewResponse::class);
    }

    /**
     * Create a new registered user.
     */
    public function store(Request $request, CreatesNewUsers $creator): RegisterResponse
    {
        /** @var \App\Actions\Fortify\CreateNewUser $creator */
        $account = $creator->request($request)->create($request->all());
        $user = $account->user();
        $building = $user->building;

        if ($account->wasRecentlyCreated) {
            $account->sendEmailVerificationNotification();
            event(new Registered($user->cooperation, $user));
        } elseif ($user->wasRecentlyCreated) {
            // We don't want to dispatch this if only a building was made
            UserAssociatedWithOtherCooperation::dispatch($user->cooperation, $user);
        }

        // At this point, a user can't register without accepting the privacy terms. If he just added a building,
        // he should have already accepted it.
        UserAllowedAccessToHisBuilding::dispatch($user, $building);

        $this->guard->login($account);

        return app(RegisterResponse::class);
    }

    /**
     * Check if a email already exists in the user table, and if it exist check if the user is registering on the wrong cooperation.
     */
    public function checkExistingEmail(Request $request, Cooperation $cooperation, ?Cooperation $forCooperation = null): JsonResponse
    {
        $cooperationToCheckFor = $forCooperation instanceof Cooperation ? $forCooperation : $cooperation;

        $email = $request->get('email');
        $account = Account::where('email', $email)->first();

        $response = [
            'email_exists' => false,
            'user_is_already_member_of_cooperation' => false,
            'user_has_no_building' => false,
        ];

        if ($account instanceof Account) {
            $response['email_exists'] = true;

            // check if the user is a member of the cooperation
            if (($user = $account->users()->forMyCooperation($cooperationToCheckFor->id)->first()) instanceof User) {
                $response['user_is_already_member_of_cooperation'] = true;

                if (! $user->building instanceof Building) {
                    $response['user_has_no_building'] = true;
                }
            }
        }

        return response()->json($response);
    }
}
