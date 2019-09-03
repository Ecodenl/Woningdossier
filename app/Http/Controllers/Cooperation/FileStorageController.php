<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Jobs\PdfReport;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Jobs\GenerateCustomQuestionnaireReport;
use App\Jobs\GenerateMeasureReport;
use App\Jobs\GenerateTotalReport;
use App\Http\Controllers\Controller;
use App\Models\InputSource;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageController extends Controller
{
    /**
     * Download method to retrieve a file from the storage
     *
     * @param  Cooperation  $cooperation
     * @param  FileType     $fileType
     * @param               $fileStorageFilename
     *
     * @return StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Cooperation $cooperation, FileType $fileType, $fileStorageFilename)
    {
        $fileStorage = $fileType
            ->files()
            ->where('filename', $fileStorageFilename)
            ->first();

        if ($fileStorage instanceof FileStorage) {

            if (\Storage::disk('downloads')->exists($fileStorageFilename)) {

                return \Storage::disk('downloads')->download($fileStorageFilename, $fileStorageFilename, [
                    'Content-type'  => $fileStorage->content_type,
                    'Pragma'        => 'no-cache',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires'       => '0',
                ]);
            } else {
                return redirect()->back()->with('warning', 'Er is iets fout gegaan');
            }
        }

        return redirect()->back();
    }



    public function store(Cooperation $cooperation, FileType $fileType)
    {
        if($fileType->isBeingProcessed()) {
            return redirect()->back();
        }

        $inputSource = HoomdossierSession::getInputSource(true);
        $user = Hoomdossier::user();

        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
        $fileName = $this->getFileNameForFileType($fileType, $user, $inputSource);

        // and delete the other available files, will trigger the observer to delete the file on disk
        foreach ($fileType->files as $fileStorage) {
            $fileStorage->delete();
            \Storage::disk('downloads')->delete($fileStorage->filename);
        }

        if ($inputSource->short == InputSource::COOPERATION_SHORT) {
            // and we create the new file
            $fileStorage = FileStorage::create([
                'cooperation_id' => $cooperation->id,
                'input_source_id' => $inputSource->id,
                'file_type_id' => $fileType->id,
                'content_type' => 'application/pdf',
                'filename' => $fileName,
            ]);
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

    }

    /**
     * Get the file name for the filetype
     *
     * @param FileType $fileType
     * @param User $user
     * @param InputSource $inputSource
     * @return mixed|string
     */
    private function getFileNameForFileType(FileType $fileType, User $user, InputSource $inputSource)
    {
        if ($fileType->short == 'pdf-report') {
            $fileName = time().'-'.Str::slug($user->getFullName()).'-'.$inputSource->name.'.pdf';
        } else {

            // create a short hash to prepend on the filename.
            $substrBycrypted = substr(\Hash::make(Str::uuid()), 7, 5);
            $substrUuid = substr(Str::uuid(), 0, 8);
            $hash = $substrUuid.$substrBycrypted;

            // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
            // Because if there are multiple jobs in the queue, only the job thats being processed would show up as "generating"
            // remove the / to prevent unwanted directories
            $fileName = str_replace('/', '',  $hash . Str::slug($fileType->name) . '.csv');
        }

        return $fileName;
    }
}
