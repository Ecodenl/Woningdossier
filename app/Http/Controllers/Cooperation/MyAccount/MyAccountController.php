<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;

class MyAccountController extends Controller
{
    public function index()
    {
        return view('cooperation.my-account.index');
    }
}
