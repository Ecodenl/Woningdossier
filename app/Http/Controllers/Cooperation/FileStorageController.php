<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Jobs\PdfReport;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Jobs\GenerateCustomQuestionnaireReport;
use App\Jobs\GenerateMeasureReport;
use App\Jobs\GenerateTotalReport;
use App\Http\Controllers\Controller;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\User;
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
                    'Content-type'  => $fileStorage->fileType->content_type,
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

    public function checkIfFileIsBeingProcessed()
    {

    }

    public function store(Cooperation $cooperation, FileType $fileType)
    {
        if($fileType->isBeingProcessed()) {
            return redirect()->back();
        }

        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);



        \Log::debug("Generate " . $fileType->short . " file..");
        \Log::debug("Context:");
        $account = Hoomdossier::account();
        $inputSourceValue = HoomdossierSession::getInputSourceValue();
        if (!is_null($inputSourceValue)){
            $inputSourceValue = \App\Helpers\Cache\InputSource::find($inputSourceValue);
        }

        $u = [
            'account' => $account->id,
            'id' => $user->id,
            'role' => HoomdossierSession::currentRole(),
            'is_observing' => HoomdossierSession::isUserObserving() ? 'yes' : 'no',
            'is_comparing' => HoomdossierSession::isUserComparingInputSources() ? 'yes' : 'no',
            'input_source' => $inputSource->short,
            'operating_on_own_building' => $building->user->id == $user->id ? 'yes' : 'no',
            'operating_as' => $inputSourceValue->short,
        ];
        $tags = [
            'building:id' => $building->id,
            'building:owner' => $building->user->id,
        ];

        \Log::debug("User info:");
        \Log::debug(json_encode($u));
        \Log::debug("Building info:");
        \Log::debug(json_encode($tags));

        \Log::debug("--- end of debug log stuff ---");
        \Log::debug(" ");




        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
        $fileName = $this->getFileNameForFileType($fileType, $user, $inputSource);

        $this->handleExistingFiles($building, $inputSource, $fileType);

        // and we create the new file
        $fileStorage = new FileStorage([
            'cooperation_id' => $cooperation->id,
            'input_source_id' => $inputSource->id,
            'file_type_id' => $fileType->id,
            'filename' => $fileName,
        ]);

        $this->authorize('store', [$fileStorage, $fileType]);

        // this is only needed when its not the cooperation generating a file.
        if ($inputSource->short != InputSource::COOPERATION_SHORT) {
            $fileStorage->building_id = $building->id;
        }

        $fileStorage->save();

        switch ($fileType->short) {
            case 'pdf-report':
                PdfReport::dispatch($user, $inputSource, $fileType, $fileStorage);
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

        return redirect($this->getRedirectUrl($inputSource))->with('success', __('woningdossier.cooperation.admin.cooperation.reports.generate.success'));

    }

    /**
     * Handle the existing files, overwrite if needed
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param FileType $fileType
     * @throws \Exception
     */
    private function handleExistingFiles(Building $building, InputSource $inputSource, FileType $fileType)
    {
        // and delete the other available files
        if ($inputSource->short != InputSource::COOPERATION_SHORT) {

            $fileStorage = $fileType->files()->forInputSource($inputSource)->where('building_id', $building->id)->first();

            if ($fileStorage instanceof FileStorage) {
                $fileStorage->delete();
                \Storage::disk('downloads')->delete($fileStorage->filename);
            }
        } else {

            $fileStorages = $fileType->files()->withExpired()->get();
            foreach ($fileStorages as $fileStorage) {
                $fileStorage->delete();
                \Storage::disk('downloads')->delete($fileStorage->filename);
            }
        }
    }

    private function getRedirectUrl(InputSource $inputSource)
    {
        if ($inputSource->short == InputSource::COOPERATION_SHORT) {
            return route('cooperation.admin.cooperation.reports.index');
        }
        return route('cooperation.tool.my-plan.index');
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
//            2013es14-Bewonster-A-g-Bewoner.pdf;

            $fileName = trim($user->building->postal_code).$user->building->number.'-'.\Illuminate\Support\Str::slug($user->getFullName()).'.pdf';

//            $fileName = time().'-'.\Illuminate\Support\Str::slug($user->getFullName()).'-'.$inputSource->name.'.pdf';
        } else {

            // create a short hash to prepend on the filename.
            $substrBycrypted = substr(\Hash::make(Str::uuid()), 7, 5);
            $substrUuid = substr(Str::uuid(), 0, 8);
            $hash = $substrUuid.$substrBycrypted;

            // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
            // Because if there are multiple jobs in the queue, only the job thats being processed would show up as "generating"
            // remove the / to prevent unwanted directories
            $fileName = str_replace('/', '',  $hash . \Illuminate\Support\Str::slug($fileType->name) . '.csv');
        }

        return $fileName;
    }
}
