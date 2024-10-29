<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;

class CoordinatorController extends Controller
{
    public function index(Cooperation $cooperation): RedirectResponse
    {
        return redirect()->route('cooperation.admin.users.index');
    }
}
