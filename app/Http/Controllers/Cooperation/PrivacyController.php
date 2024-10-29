<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\View\View;
use App\Http\Controllers\Controller;

class PrivacyController extends Controller
{
    public function index(): View
    {
        return view('cooperation.privacy.index');
    }
}
