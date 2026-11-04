<?php

namespace App\Http\ViewComposers\Frontend\Layouts\Parts;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Services\SmartTwin\SmartTwinHandOff;
use App\Services\SmartTwin\WoonplanLock;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * What a coach opening the woonplan of a resident's quick scan should do first: the resident made a
 * woonplan with SmartTwin's quick scan, which the coach turns into an advice by looking at the
 * dwelling and then going into SmartTwin.
 *
 * Only while the woonplan is the resident's: once the coach's own advice is in (the last import came
 * from the advisor tool, WoonplanLock::hasCoachPlan()), there is nothing left to point at.
 */
class SmartTwinCoachNoticeComposer
{
    public function __construct(private Request $request)
    {
    }

    public function create(View $view): void
    {
        /** @var Scan $scan */
        $scan = $this->request->route('scan');
        $building = HoomdossierSession::getBuilding(true);
        $inputSource = HoomdossierSession::getInputSource(true);

        $show = $this->request->routeIs('cooperation.frontend.tool.simple-scan.my-plan.index')
            && $scan->isQuickScan()
            && $building instanceof Building
            && $inputSource instanceof InputSource
            && InputSource::COACH_SHORT === $inputSource->short
            && ! HoomdossierSession::isUserObserving()
            && Hoomdossier::smartTwinOwnsWoonplanFor($building)
            && ! app(WoonplanLock::class)->hasCoachPlan($building);

        $view->with([
            'show' => $show,
            'scan' => $scan,
            'canHandOff' => $show && SmartTwinHandOff::isOfferedFor($building),
        ]);
    }
}
