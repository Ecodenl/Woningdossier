<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $this->middleware('auth')->only('show');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('cooperation.auth.verify');
    }

    public function oldVerifyUrl()
    {
        return redirect()->route('cooperation.auth.login')->with('warning', __('cooperation/auth/verify.old-url'));
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $account = Account::find($request->route('id'));

        if (! hash_equals((string) $request->route('id'), (string) $account->getKey())) {
            Log::error(__METHOD__ . " !hash equals");
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($account->getEmailForVerification()))) {
            Log::error(__METHOD__ ." sha1 mismatch");
            throw new AuthorizationException;
        }

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
