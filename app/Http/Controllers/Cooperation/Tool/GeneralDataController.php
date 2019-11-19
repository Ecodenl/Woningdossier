<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Controllers\Controller;

class GeneralDataController extends Controller
{
    /**
     * Just here to redirect!
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('cooperation.tool.general-data.interest.index');
    }
}
