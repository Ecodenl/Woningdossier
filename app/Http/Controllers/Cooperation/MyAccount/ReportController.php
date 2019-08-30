<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Helpers\Str;
use App\Jobs\PdfReport;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\FileTypeCategory;
use App\Models\InputSource;
use App\Models\User;
use App\Scopes\AvailableScope;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        dd(StepHelper::getAllCommentsByStep(Hoomdossier::user()));

        $reportFileTypeCategory = FileTypeCategory::short('report')->with(['fileTypes' => function ($query) {
            $query->where('short', 'pdf-report');
        }])->first();


        $anyFilesBeingProcessed = FileStorage::withOutGlobalScope(new AvailableScope())->where('is_being_processed', true)->count();

        return view('cooperation.my-account.reports.index', compact('reportFileTypeCategory', 'anyFilesBeingProcessed'));
    }

    public function generate(Cooperation $cooperation, FileType $fileType)
    {
        if($fileType->isBeingProcessed()) {
            return redirect()->back();
        }

        // create a short hash to prepend on the filename.
        $substrBycrypted = substr(\Hash::make(Str::uuid()), 7, 5);
        $substrUuid = substr(Str::uuid(), 0, 8);
        $hash = $substrUuid.$substrBycrypted;

        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
        // Because if there are multiple jobs in the queue, only the job thats being processed would show up as "generating"
        // remove the / to prevent unwanted directories
        $fileName = str_replace('/', '',  $hash . \Illuminate\Support\Str::slug($fileType->name) . '.pdf');

        // and delete the other available files, will trigger the observer to delete the file on disk
        foreach ($fileType->files as $fileStorage) {
            $fileStorage->delete();
            \Storage::disk('downloads')->delete($fileStorage->filename);
        }

        // and we create the new file
        $fileStorage = FileStorage::create([
            'user_id' => Hoomdossier::user()->id,
            'cooperation_id' => $cooperation->id,
            'file_type_id' => $fileType->id,
            'content_type' => 'application/pdf',
            'filename' => $fileName,
        ]);

        switch ($fileType->short) {
            case 'pdf-report':
                PdfReport::dispatch(Hoomdossier::user(), HoomdossierSession::getInputSource(true), $fileType, $fileStorage);
                break;

        }

        return redirect(route('cooperation.my-account.report.index'))
            ->with('success',  __('woningdossier.cooperation.admin.cooperation.reports.generate.success'));
    }

}
