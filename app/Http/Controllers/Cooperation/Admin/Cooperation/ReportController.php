<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation;

use App\Helpers\Str;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateCustomQuestionnaireReport;
use App\Jobs\GenerateMeasureReport;
use App\Jobs\GenerateTotalReport;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\FileTypeCategory;
use App\Scopes\AvailableScope;
use Illuminate\Support\Facades\Hash;

class ReportController extends Controller
{
    public function index()
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')->with('fileTypes.files')->first();

        $anyFilesBeingProcessed = FileStorage::withOutGlobalScope(new AvailableScope())->where('is_being_processed', true)->count();

        return view('cooperation.admin.cooperation.reports.index', compact('reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }

    /**
     * Method that handles the right download by id.
     *
     * @param Cooperation $cooperation
     * @param FileType    $fileType
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function generate(Cooperation $cooperation, FileType $fileType)
    {
        if ($fileType->isBeingProcessed()) {
            return redirect()->back();
        }

        // create a short hash to prepend on the filename.
        $substrBycrypted = substr(Hash::make(Str::uuid()), 7, 5);
        $substrUuid = substr(Str::uuid(), 0, 8);
        $hash = $substrUuid.$substrBycrypted;

        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
        // Because if there are multiple jobs in the queue, only the job thats being processed would show up as "generating"
        // remove the / to prevent unwanted directories
        $fileName = str_replace('/', '', $hash.\Illuminate\Support\Str::slug($fileType->name).'.csv');

        // and delete the other available files, will trigger the observer to delete the file on disk
        foreach ($fileType->files as $fileStorage) {
            $fileStorage->delete();
            \Storage::disk('downloads')->delete($fileStorage->filename);
        }

        // and we create the new file
        $fileStorage = FileStorage::create([
            'cooperation_id' => $cooperation->id,
            'file_type_id' => $fileType->id,
            'content_type' => 'text/csv',
            'filename' => $fileName,
        ]);

        switch ($fileType->short) {
            case 'total-report':
                GenerateTotalReport::dispatch($cooperation, $fileType, $fileStorage);
                break;
            case 'total-report-anonymized':
                GenerateTotalReport::dispatch($cooperation, $fileType, $fileStorage, true);
                break;
            case 'measure-report':
                GenerateMeasureReport::dispatch($cooperation, $fileType, $fileStorage);
                break;
            case 'measure-report-anonymized':
                GenerateMeasureReport::dispatch($cooperation, $fileType, $fileStorage, true);
                break;
            case 'custom-questionnaires-report':
                GenerateCustomQuestionnaireReport::dispatch($cooperation, $fileType, $fileStorage);
                break;
            case 'custom-questionnaires-report-anonymized':
                GenerateCustomQuestionnaireReport::dispatch($cooperation, $fileType, $fileStorage, true);
                break;
        }

        return redirect(route('cooperation.admin.cooperation.reports.index'))
            ->with('success', __('woningdossier.cooperation.admin.cooperation.reports.generate.success'));
    }
}
