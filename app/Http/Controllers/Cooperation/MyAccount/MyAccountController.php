<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Http\Controllers\Controller;

class MyAccountController extends Controller
{
    public function index()
    {
        return redirect(route('cooperation.my-account.messages.index'));
    }
}
