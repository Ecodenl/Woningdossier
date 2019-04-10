<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\Kengetallen;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KeyFiguresController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // we handle translations in the view.
        $keyfigures  = (new \ReflectionClass(Kengetallen::class))->getConstants();

        $measureApplications = MeasureApplication::all();

        return view('cooperation.admin.super-admin.key-figures.index', compact(
            'keyfigures', 'measureApplications'
        ));
    }
}
