<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Jobs\PdfReport;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\FileTypeCategory;
use App\Scopes\AvailableScope;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        $reportFileTypeCategory = FileTypeCategory::short('report')
            ->with(['fileTypes' => function ($query) {
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

        $inputSource = HoomdossierSession::getInputSource(true);
        $user = Hoomdossier::user();

        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.

        $fileName = time().'-'.Str::slug($user->getFullName()).'-'.$inputSource->name.'.pdf';

        // and delete the other available files, will trigger the observer to delete the file on disk
        foreach ($fileType->files as $fileStorage) {
            $fileStorage->delete();
            \Storage::disk('downloads')->delete($fileStorage->filename);
        }

        // and we create the new file
        $fileStorage = FileStorage::create([
            'user_id' => $user->id,
            'cooperation_id' => $cooperation->id,
            'file_type_id' => $fileType->id,
            'content_type' => 'application/pdf',
            'filename' => $fileName,
        ]);

        switch ($fileType->short) {
            case 'pdf-report':
                PdfReport::dispatch(Hoomdossier::user(), $inputSource, $fileType, $fileStorage);
                break;

        }

        return redirect(route('cooperation.my-account.report.index'))
            ->with('success',  __('woningdossier.cooperation.admin.cooperation.reports.generate.success'));
    }

}
