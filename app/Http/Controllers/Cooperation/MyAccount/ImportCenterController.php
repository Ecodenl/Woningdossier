<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Services\ToolSettingService;
use Illuminate\Http\Request;

class ImportCenterController extends Controller
{
    /**
     * Set the compare sessions, if the user is not comparing set the compare to true; else we leave it to false.
     *
     * @param $inputSourceShort
     *
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
     */
    public function dismissNotification(Cooperation $cooperation, Request $request)
    {
        // dissmis the notification
        $inputSource = InputSource::findByShort($request->get('input_source_short'));

        ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $inputSource->id, false);
    }
}
