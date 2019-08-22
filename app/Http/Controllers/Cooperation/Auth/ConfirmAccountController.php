<?php

namespace App\Http\Controllers\Cooperation\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Auth\ConfirmRequest;
use App\Models\Account;
use App\Models\Cooperation;

class ConfirmAccountController extends Controller
{
    public function store(ConfirmRequest $request, Cooperation $cooperation)
    {
        $email = $request->get('u');
        $token = $request->get('t');

        $account = Account::where('email', $email)->where('confirm_token', $token)->first();

        $account->confirm_token = null;
        $account->save();

        return redirect()->route('cooperation.login', ['cooperation' => $cooperation])
                         ->with('success', __('auth.confirm.success'));
    }
}
