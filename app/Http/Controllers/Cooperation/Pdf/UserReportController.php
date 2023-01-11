<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Calculations\Heater;
use App\Calculations\HeatPump;
use App\Calculations\HighEfficiencyBoiler;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Helpers\ToolHelper;
use App\Helpers\ToolQuestionHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\ToolCalculationResult;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdviceComments;
use App\Services\BuildingCoachStatusService;
use App\Services\DumpService;
use App\Services\UserActionPlanAdviceService;
use App\Services\UserService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserReportController extends Controller
{
    /**
     * TESTING only.
     */
    public function index(Cooperation $cooperation)
    {
        // Always retrieve from master
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $GLOBALS['_cooperation'] = $cooperation;

        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        // TODO: Define short somehow
        if (true) {
            $short = ToolHelper::STRUCT_PDF_QUICK;
        } else {
            $short = ToolHelper::STRUCT_PDF_LITE;
        }

        $dumpService = DumpService::init()->inputSource($inputSource)
            ->user($user)
            ->createHeaderStructure($short, false);

        $headers = $dumpService->headerStructure;

        //$dump = $dumpService->generateDump();

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = User::whereIn('id', $connectedCoaches->pluck('coach_id')->toArray())
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS full_name")
            ->pluck('full_name')
            ->toArray();

        $pdf = Pdf::loadView('cooperation.pdf.user-report.index', compact(
            'cooperation', 'building', 'user',
            'connectedCoachNames'
        ));

        return $pdf->stream();
    }
}
