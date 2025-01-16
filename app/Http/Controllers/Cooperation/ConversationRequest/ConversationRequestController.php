<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Deprecation\DeprecationLogger;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Services\Models\BuildingStatusService;
use App\Services\PrivateMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConversationRequestController extends Controller
{
    public function index(Cooperation $cooperation, ?string $requestType, ?string $measureApplicationShort = null): View|RedirectResponse
    {
        DeprecationLogger::alert(__CLASS__ . ' used!');
        $scan = $cooperation->scans()->where('scans.short', '!=', Scan::EXPERT)->first();

        // if the user is observing, he has nothing to do here.
        if (HoomdossierSession::isUserObserving()) {
            return redirect()->route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'));
        }

        $title = __('conversation-requests.index.request-coach-conversation');

        $measureApplicationName = null;
        if (! is_null($measureApplicationShort)) {
            $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->firstOrFail();
            // set the measure application name if there is a measure application
            $measureApplicationName = $measureApplication->measure_name;
            $title = __('conversation-requests.index.form.title', ['measure_application_name' => $measureApplicationName]);
        }

        return view('cooperation.conversation-requests.index', compact('scan', 'requestType', 'measureApplicationName', 'title'));
    }

    /**
     * Save the conversation request for whatever the conversation request may be.
     */
    public function store(BuildingStatusService $buildingStatusService, ConversationRequest $request, Cooperation $cooperation): RedirectResponse
    {
        DeprecationLogger::alert(__CLASS__ . ' used!');
        PrivateMessageService::createConversationRequest(HoomdossierSession::getBuilding(true), Hoomdossier::user(), $request);

        $buildingStatusService->forBuilding(HoomdossierSession::getBuilding(true))->setStatus('pending');

        $successMessage = __('conversation-requests.store.success.' . InputSource::COACH_SHORT);

        if (InputSource::RESIDENT_SHORT == HoomdossierSession::getInputSource(true)->short) {
            $successMessage = __('conversation-requests.store.success.' . InputSource::RESIDENT_SHORT, ['url' => route('cooperation.my-account.messages.edit', compact('cooperation'))]);
        }

        $scan = $cooperation->scans()->where('scans.short', '!=', Scan::EXPERT)->first();
        return redirect()->route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan'))
            ->with('success', $successMessage);
    }
}
