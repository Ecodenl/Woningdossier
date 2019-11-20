<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Database\Query\Builder;

class GeneralDataController extends Controller
{
    /**
     * Just here to redirect!
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $building = HoomdossierSession::getBuilding(true);

        // in short: when all the substeps are finished redirect to interest page
        // else to the characteristics sub step
        $completedSubStepsOfGeneralData = $building->completedSteps()->whereNotExists(function (Builder $query) {
            $query->select('*')
                ->from('steps')
                ->whereNull('steps.parent_id')
                ->whereRaw('completed_steps.step_id = steps.id');
        })->get();

        if (Step::onlySubSteps()->count() == $completedSubStepsOfGeneralData->count()) {
            return redirect()->route('cooperation.tool.general-data.interest.index');
        }
        return redirect()->route('cooperation.tool.general-data.building-characteristics.index');
    }
}
