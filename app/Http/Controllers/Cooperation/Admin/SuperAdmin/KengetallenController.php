<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Helpers\Kengetallen;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\VarDumper\Tests\Caster\ReflectionCasterTest;

class KengetallenController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // we handle translations in the view.
        $kengetallen  = (new \ReflectionClass(Kengetallen::class))->getConstants();

        $measureApplications = MeasureApplication::all();

        return view('cooperation.admin.super-admin.kengetallen.index', compact(
            'kengetallen', 'measureApplications'
        ));
    }
}
