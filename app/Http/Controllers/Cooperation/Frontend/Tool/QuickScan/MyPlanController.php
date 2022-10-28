<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\QuickScan;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\Notification;
use App\Models\Step;
use App\Models\SubStep;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function index()
    {
        /** @var Building $building */
        $building = HoomdossierSession::getBuilding(true);

        // For quick testing purposes, we really don't want to run through the tool each time
        if (! app()->environment('local')) {
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            $firstIncompleteStep = $building->getFirstIncompleteStep([], $masterInputSource);

            // There are incomplete steps left, set the sub step
            if ($firstIncompleteStep instanceof Step) {
                $firstIncompleteSubStep = $building->getFirstIncompleteSubStep($firstIncompleteStep, [], $masterInputSource);

                if ($firstIncompleteSubStep instanceof SubStep) {
                    return redirect()->route('cooperation.frontend.tool.quick-scan.index', [
                        'step' => $firstIncompleteStep,
                        'subStep' => $firstIncompleteSubStep,
                    ]);
                }
            }
        }

        $notification = Notification::activeNotifications($building,
            InputSource::findByShort(InputSource::MASTER_SHORT))->first();

        return view('cooperation.frontend.tool.quick-scan.my-plan.index', compact('building', 'notification'));
    }

    public function media(Request $request)
    {
        $this->authorize('viewAny', [Media::class, HoomdossierSession::getInputSource(true), HoomdossierSession::getBuilding(true)]);

        $buildingId = $request->input('building_id', HoomdossierSession::getBuilding());
        $building = \App\Helpers\Cache\Building::find($buildingId);

        return view('cooperation.frontend.tool.quick-scan.my-plan.media', compact('building'));
    }
}
