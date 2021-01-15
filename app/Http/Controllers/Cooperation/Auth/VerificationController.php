<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('cooperation.auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        $account = Account::find($request->route('id'));

        // this method can be and should be adjusted in the next shift
        // the next version will have a extra hash in the url, we can use this to compare the email.

        dd($acc)
        if ($account->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($account->markEmailAsVerified()) {
            event(new Verified($account));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    public function redirectTo()
    {
        if (Auth::check()) {
            return $this->redirectTo;
        }
        return route('cooperation.auth.login');
    }
}