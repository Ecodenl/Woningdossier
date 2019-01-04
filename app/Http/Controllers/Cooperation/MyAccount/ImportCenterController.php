<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportCenterController extends Controller
{
    public function index()
    {
        return view('cooperation.my-account.import-center.index');
    }
}