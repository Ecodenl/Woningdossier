<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index()
    {
//        $buildings = $
        return view('cooperation.admin.cooperation.messages.index', compact('buildings'));
    }
}
