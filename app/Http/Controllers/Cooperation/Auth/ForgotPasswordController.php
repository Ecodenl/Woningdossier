<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('cooperation.auth.passwords.email');
    }

    /**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $this->validate($request, ['email' => [
                'required',
                'email',
                // we have to validate if the email is a member of this cooperation.
                function ($attribute, $value, $fail) {
                    $user = Account::where('email', $value)->first()->users()->withoutGlobalScopes()->where('cooperation_id', HoomdossierSession::getCooperation())->first();
                    if (! $user instanceof User) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],
        ]);
    }
}
