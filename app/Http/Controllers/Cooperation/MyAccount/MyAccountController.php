<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class MyAccountController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        return view('cooperation.my-account.index');
    }
}
