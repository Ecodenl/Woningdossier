<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolSetting;
use App\Http\Controllers\Controller;
use App\Services\ToolSettingService;
use Illuminate\Http\Request;

class ImportCenterController extends Controller
{
    public function index()
    {
        $toolSettings = ToolSetting::getChangedSettings(HoomdossierSession::getBuilding());

        return view('cooperation.my-account.import-center.index', compact('toolSettings'));
    }

    /**
     * Set the compare sessions, if the user is not comparing set the compare to true; else we leave it to false.
     *
     * @param Cooperation $cooperation
     * @param $inputSourceShort
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setCompareSession(Cooperation $cooperation, $inputSourceShort)
    {
        $compare = false;

        if (HoomdossierSession::isUserNotComparingInputSources()) {
            $compare = true;
        }

        HoomdossierSession::setCompareInputSourceShort($inputSourceShort);
        HoomdossierSession::setIsUserComparingInputSources($compare);

        return redirect()->route('cooperation.tool.general-data.index');
    }

    /**
     * Dismiss the notification from the tool pages / set the changed column to false.
     *
     * @param Cooperation $cooperation
     * @param Request $request
     */
    public function dismissNotification(Cooperation $cooperation, Request $request)
    {
        $inputSource = InputSource::findByShort($request->get('input_source_short'));

        ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $inputSource->id, false);
    }
}