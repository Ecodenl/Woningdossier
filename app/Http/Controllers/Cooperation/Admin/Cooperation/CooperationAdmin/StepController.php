<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        // since the general-data always need to be the first step and is always needed
        $steps = $cooperation->steps()->orderBy('cooperation_steps.order')->where('slug', '!=', 'general-data')->get();

        return view('cooperation.admin.cooperation.cooperation-admin.steps.index', compact('steps'));
    }

    /**
     * Set the active status for a cooperation step.
     *
     * @param Request     $request
     * @param Cooperation $cooperation
     */
    public function setActive(Request $request, Cooperation $cooperation)
    {
        $stepId = $request->get('step_id');
        $active = 'true' == $request->get('step_active') ? true : false;

        // get the cooperation steps query
        $cooperationStepsQuery = $cooperation->steps();
        // now find the selected step
        $cooperationStep = $cooperationStepsQuery->find($stepId);
        if ($cooperationStep instanceof Step) {
            // update the pivot table / cooperation_step
            $cooperationStepsQuery->updateExistingPivot($cooperationStep->id, ['is_active' => $active]);
        }
    }
}
