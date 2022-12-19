<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Models\Account;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class AuthenticatedSessionController extends \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController
{
    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
    {
        // validate the credentials from the user
        if (Auth::guard()->validate($this->credentials($request))) {
            $cooperation = $request->route('cooperation');

            /** @var Account $account */
            $account = Auth::guard()->getLastAttempted();

            if (! $account->isAssociatedWith($cooperation)) {
                throw ValidationException::withMessages(['cooperation' => [trans('auth.cooperation')]]);
            }

            if (! $account->user()->building instanceof Building) {
                Log::error('no building attached for user id: '.$account->user()->id.' account id:'.$account->id);

                return redirect(route('cooperation.create-building.index'))->with('warning', __('auth.login.warning'));
            }
        }

        return parent::store($request);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only(Fortify::username(), 'password'), ['active' => 1]);
    }
}
