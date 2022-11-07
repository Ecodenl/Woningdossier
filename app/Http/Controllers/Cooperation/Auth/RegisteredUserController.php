<?php

namespace App\Http\Controllers\Cooperation\Auth;

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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Laravel\Fortify\Contracts\RegisterViewResponse|mixed
     */
    public function index(Request $request)
    {
        $registerUrl = CooperationSettingHelper::getSettingValue($request->route('cooperation'),
            CooperationSettingHelper::SHORT_REGISTER_URL);

        if (! empty($registerUrl)) {
            return redirect()->away($registerUrl);
        }

        return app(RegisterViewResponse::class);
    }

    /**
     * Create a new registered user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Laravel\Fortify\Contracts\CreatesNewUsers $creator
     *
     * @return \Laravel\Fortify\Contracts\RegisterResponse
     */
    public function store(Request $request, CreatesNewUsers $creator): RegisterResponse
    {
        /** @var \App\Actions\Fortify\CreateNewUser $creator */
        $user = $creator->request($request)->create($request->all());
        $account = $user->account;

        if ($account->wasRecentlyCreated) {
            $account->sendEmailVerificationNotification();
            event(new Registered($user->cooperation, $user));
        } else {
            UserAssociatedWithOtherCooperation::dispatch($user->cooperation, $user);
        }
        // at this point, a user can't register without accepting the privacy terms.
        UserAllowedAccessToHisBuilding::dispatch($user->building);

        $this->guard->login($account);

        return app(RegisterResponse::class);
    }

    /**
     * Check if a email already exists in the user table, and if it exist check if the user is registering on the wrong cooperation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkExistingEmail(Cooperation $cooperation, Request $request)
    {
        $email = $request->get('email');
        $account = Account::where('email', $email)->first();

        $response = ['email_exists' => false, 'user_is_already_member_of_cooperation' => false];

        if ($account instanceof Account) {
            $response['email_exists'] = true;

            // check if the user is a member of the cooperation
            if ($account->user() instanceof User) {
                $response['user_is_already_member_of_cooperation'] = true;
            }

            return response()->json($response);
        }

        return response()->json($response);
    }
}
