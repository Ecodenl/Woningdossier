<?php

namespace App\Http\Controllers\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Services\SmartTwin\SmartTwinDeeplinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SmartTwinHandoffController extends Controller
{
    public function __invoke(Cooperation $cooperation, Scan $scan, SmartTwinDeeplinkService $service): View|RedirectResponse
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = Hoomdossier::user();

        if (! Hoomdossier::hasEnabledSmartTwinCalls() || ! $building instanceof Building || ! $user) {
            return $this->backToWoonplan($cooperation, $scan);
        }

        // Note this now sets the role from the session. During handoff the role is explicitly checked against 'coach'. This means that if the user is
        // currently logged in as cooperation-admin or coordinator, this will result in a resident (because fallback) handoff. Is this acceptable behavior?
        $roleName = HoomdossierSession::getRole(true)?->name ?? RoleHelper::ROLE_RESIDENT;

        $result = $service->handoff($user, $building, $roleName);

        if (! $result->isSuccessful()) {
            return $this->backToWoonplan($cooperation, $scan, $result->status);
        }

        // Bare bridge page: the browser POSTs the JWT to SmartTwin so the login
        // session + redirect land in the user's browser (see view for the why).
        return view('cooperation.frontend.tool.simple-scan.my-plan.smarttwin-handoff', [
            'url'   => $result->url,
            'token' => $result->token,
        ]);
    }

    private function backToWoonplan(Cooperation $cooperation, Scan $scan, ?string $error = null): RedirectResponse
    {
        $parameters = compact('cooperation', 'scan');

        if ($error !== null) {
            $parameters['smarttwin_error'] = $error;
        }

        return redirect()->route('cooperation.frontend.tool.simple-scan.my-plan.index', $parameters);
    }
}
