<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Helpers\MediaHelper;
use App\Helpers\Models\CooperationSettingHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\ScansFormRequest;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\SettingsFormRequest;
use App\Models\Cooperation;
use App\Models\CooperationScan;
use App\Models\Scan;
use App\Services\CooperationScanService;
use Illuminate\Http\UploadedFile;
use Plank\Mediable\Facades\MediaUploader;
use App\Models\Media;

class ScanController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $scans = Scan::all();
        $currentScan = CooperationScanService::init($cooperation)->getCurrentType();
        $mapping = CooperationScanService::translationMap();

        return view('cooperation.admin.cooperation.cooperation-admin.scans.index', compact('scans', 'mapping', 'currentScan'));
    }

    public function store(ScansFormRequest $request, Cooperation $cooperation)
    {
        CooperationScanService::init($cooperation)->syncScan($request->input('scans.type'));


        return redirect()->back()
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/scans.store.success'));
    }
}
