<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Controllers\Controller;

class ToolController extends Controller
{
    /**
     * Redirect to the general data step since the tool view has no content.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        return redirect()->route('cooperation.tool.ventilation.index');
//        return redirect(route('cooperation.tool.general-data.index'));
    }
}
