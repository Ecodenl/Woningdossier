<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResendConfirmMailRequest;
use App\Jobs\SendRequestAccountConfirmationEmail;
use App\Models\Account;
use App\Models\Cooperation;

class ResendConfirmAccountController extends Controller
{
    public function show()
    {
        return view('cooperation.auth.confirm.resend.show');
    }

    public function store(Cooperation $cooperation, ResendConfirmMailRequest $request)
    {
        $validated = $request->validated();

        $account = Account::where('email', '=', $validated['email'])->whereNotNull('confirm_token')->first();

        SendRequestAccountConfirmationEmail::dispatch($account->user(), $cooperation);

        return redirect()->route('cooperation.auth.confirm.resend.show', ['cooperation' => $cooperation])
                         ->with('success', trans('auth.confirm.email-success'));
    }
}
