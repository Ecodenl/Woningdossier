<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Models\Step;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToolController extends Controller
{
    protected $step;

    public function __construct(Request $request) {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Redirect to the general data step since the tool view has no content
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index(){
        $cooperation = Cooperation::find(\Session::get('cooperation'));

        return redirect(route('cooperation.tool.general-data.index', ['cooperation' => $cooperation]));
    }
}
