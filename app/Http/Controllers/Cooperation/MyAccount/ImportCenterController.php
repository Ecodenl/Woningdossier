<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolSetting;
use App\Http\Controllers\Controller;

class ImportCenterController extends Controller
{
    public function index()
    {
        $toolSettings = ToolSetting::getChangedSettings(HoomdossierSession::getBuilding());

        return view('cooperation.my-account.import-center.index', compact('toolSettings'));
    }

    public function setCompareSession(Cooperation $cooperation, $inputSourceShort)
    {
        HoomdossierSession::setIsUserComparingInputSources(true);
        HoomdossierSession::setCompareInputSourceShort($inputSourceShort);

        return redirect()->route('cooperation.tool.general-data.index');
    }
}