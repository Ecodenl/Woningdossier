<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
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
    public function index(Cooperation $cooperation): View
    {
        $scans = Scan::all();
        $currentScan = CooperationScanService::init($cooperation)->getCurrentType();
        $mapping = CooperationScanService::translationMap();

        // Haal huidige small_measures_enabled instellingen op
        $smallMeasuresSettings = [];
        foreach ($cooperation->scans as $scan) {
            $smallMeasuresSettings[$scan->short] = $scan->pivot->small_measures_enabled ?? true;
        }

        return view('cooperation.admin.cooperation.cooperation-admin.scans.index', compact('scans', 'mapping', 'currentScan', 'smallMeasuresSettings'));
    }

    public function store(ScansFormRequest $request, Cooperation $cooperation): RedirectResponse
    {
        CooperationScanService::init($cooperation)->syncScan($request->input('scans.type'));

        // Sync small measures instellingen
        $smallMeasuresEnabled = $request->input('scans.small_measures_enabled', []);

        foreach ($cooperation->scans()->get() as $scan) {
            $cooperation->scans()->updateExistingPivot($scan->id, [
                'small_measures_enabled' => isset($smallMeasuresEnabled[$scan->short])
                    ? filter_var($smallMeasuresEnabled[$scan->short], FILTER_VALIDATE_BOOLEAN)
                    : true,
            ]);
        }

        return redirect()->back()
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/scans.store.success'));
    }
}
