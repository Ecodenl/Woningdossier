<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\InputSource;
use App\Services\BuildingDataCopyService;
use App\Services\ToolSettingService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    /**
     * @param Request $request
     *
     * @note if there are "bugs" or problems, check if the tables have the right where columns etc.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy(Request $request)
    {
        $building = HoomdossierSession::getBuilding(true);
        // the input source to copy from.
        $desiredInputSourceName = $request->get('input_source');
        $desiredInputSource = InputSource::findByShort($desiredInputSourceName);
        $targetInputSource = HoomdossierSession::getInputSource(true);

        BuildingDataCopyService::copy($building, $desiredInputSource, $targetInputSource);

        ToolSettingService::setChanged(HoomdossierSession::getBuilding(), $desiredInputSource->id, false);
        HoomdossierSession::stopUserComparingInputSources();

        return redirect()->back();
    }
}
