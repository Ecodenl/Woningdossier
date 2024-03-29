<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\FileStorageFormRequest;
use App\Jobs\GenerateCustomQuestionnaireReport;
use App\Jobs\GenerateToolReport;
use App\Jobs\PdfReport;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\User;
use App\Services\FileStorageService;
use App\Services\FileTypeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileStorageController extends Controller
{
    /**
     * Download method to retrieve a file from the storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(Cooperation $cooperation, FileStorage $fileStorage)
    {
        $building = HoomdossierSession::getBuilding(true);
        // because of the global scope on the file storage its impossible to retrieve a file from a other cooperation
        // but we will still do some additional checks
        $this->authorize('download', [$fileStorage, $building]);

        return FileStorageService::download($fileStorage);
    }

    /**
     * * Check whether a file type is being processed for the user / input source.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIfFileIsBeingProcessed(Cooperation $cooperation, FileType $fileType)
    {
        $user = Hoomdossier::user();
        $inputSource = HoomdossierSession::getInputSource(true);

        if ($user->hasRoleAndIsCurrentRole(['cooperation-admin', 'coordinator']) && 'pdf-report' != $fileType->short) {
            $isFileBeingProcessed = FileStorageService::isFileTypeBeingProcessedForCooperation($fileType, $cooperation);
            $fileStorage = $fileType->files()->first();
            $downloadLinkForFileType = route('cooperation.file-storage.download', compact('fileStorage'));

            if ($fileStorage instanceof FileStorage) {
                $this->authorize('download', $fileStorage);
            }
        } else {
            $building = HoomdossierSession::getBuilding(true);
            $buildingOwner = $building->user;

            $isFileBeingProcessed = FileStorageService::isFileTypeBeingProcessedForUser($fileType, $buildingOwner, $inputSource);
            $fileStorage = $fileType->files()->forMe($buildingOwner)->forInputSource($inputSource)->first();
            $downloadLinkForFileType = $fileStorage instanceof FileStorage ? route('cooperation.file-storage.download', compact('fileStorage')) : null;

            // as this checks for file processing, there's a chance it isn't picked up by the queue
            // so we check if it actually exisits
            if ($fileStorage instanceof FileStorage) {
                $this->authorize('download', [$fileStorage, $building]);
            }
        }

        return response()->json([
            'file_created_at' => $fileStorage instanceof FileStorage ? $fileStorage->created_at->format('Y-m-d H:i') : null,
            'file_type_name' => $fileType->name,
            'is_file_being_processed' => $isFileBeingProcessed,
            'file_download_link' => $downloadLinkForFileType,
        ]);
    }

    public function store(Cooperation $cooperation, FileType $fileType, FileStorageFormRequest $request)
    {
        if ($fileType->isBeingProcessed()) {
            return redirect()->back();
        }

        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        $inputSource = HoomdossierSession::getInputSource(true);

        $questionnaire = Questionnaire::find($request->input('file_storages.questionnaire_id'));

        Log::debug('Generate '.$fileType->short.' file..');
        Log::debug('Context:');
        $account = Hoomdossier::account();
        $inputSourceValue = HoomdossierSession::getInputSourceValue();
        if (! is_null($inputSourceValue)) {
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

        Log::debug('User info:');
        Log::debug(json_encode($u));
        Log::debug('Building info:');
        Log::debug(json_encode($tags));

        Log::debug('--- end of debug log stuff ---');
        Log::debug(' ');

        // we will create the file storage here, if we would do it in the job itself it would bring confusion to the user.
        $fileName = $this->getFileNameForFileType($fileType, $user, $inputSource);

        $this->handleExistingFiles($building, $inputSource, $fileType, $questionnaire);

        // and we create the new file
        $fileStorage = new FileStorage([
            'cooperation_id' => $cooperation->id,
            'input_source_id' => $inputSource->id,
            'file_type_id' => $fileType->id,
            'filename' => $fileName,
        ]);

        $this->authorize('store', [$fileStorage, $fileType]);

        // this is only needed when its not the cooperation generating a file.
        if (InputSource::COOPERATION_SHORT != $inputSource->short) {
            $fileStorage->building_id = $building->id;
        }

        $fileStorage->save();

        // flash messages will be stored here
        $with = [];
        $anonymized = Str::contains($fileType->short, 'anonymized');

        switch ($fileType->short) {
            //case 'pdf-report':
            //    NOTE: Currently NOT used, however if this should be usable again, the parameters should be updated.
            //    $with = ['success' => __('woningdossier.cooperation.admin.cooperation.reports.generate.success')];
            //    PdfReport::dispatch($user, $inputSource, $fileType, $fileStorage);
            //    break;
            case 'total-report':
            case 'total-report-anonymized':
            case 'lite-scan-report':
            case 'lite-scan-report-anonymized':
            case 'small-measures-report':
            case 'small-measures-report-anonymized':
                GenerateToolReport::dispatch($cooperation, $fileType, $fileStorage, $anonymized);
                break;
            case 'custom-questionnaire-report':
            case 'custom-questionnaire-report-anonymized':
                $date = Carbon::now()->format('y-m-d');
                $questionnaireName = Str::slug($questionnaire->name);

                $filePart = $anonymized ? 'zonder' : 'met';

                $filename = "{$date}-{$questionnaireName}-{$filePart}-adresgegevens.csv";

                $fileStorage->update([
                    'filename' => $filename,
                    'questionnaire_id' => $questionnaire->id,
                ]);
                GenerateCustomQuestionnaireReport::dispatch($questionnaire, $filename, $fileType, $fileStorage, $anonymized);
                break;
        }

        return redirect($this->getRedirectUrl($cooperation, $inputSource))->with($with);
    }

    /**
     * Handle the existing files, overwrite if needed.
     *
     * @throws \Exception
     */
    private function handleExistingFiles(Building $building, InputSource $inputSource, FileType $fileType, Questionnaire $questionnaire = null)
    {
        // For the users: delete the other existing file storages and files for the given file type.
        if (InputSource::COOPERATION_SHORT != $inputSource->short) {
            // with expired, otherwise the expired files will never be deleted.
            $fileStorages = $fileType
                ->files()
                ->withExpired()
                ->forMe($building->user)
                ->forInputSource($inputSource)
                ->get();
        } else {
            // For the cooperation: delete the other existing file storages and files for given file type
            $fileStorages = $fileType
                ->files()
                // just to be sure, the building id should never be filled when a csv report is generated for the cooperation
                // but better safe than deleting the whole cooperation->users file storages.
                ->whereNull('building_id')
                ->withExpired()->get();

            if ($questionnaire instanceof Questionnaire) {
                $fileStorages = $fileType
                    ->files()
                    ->whereNull('building_id')
                    ->where('questionnaire_id', $questionnaire->id)
                    ->withExpired()
                    ->get();
            }
        }

        foreach ($fileStorages as $fileStorage) {
            FileStorageService::delete($fileStorage);
        }
    }

    /**
     * @param \App\Models\Cooperation $cooperation
     * @param \App\Models\InputSource $inputSource
     *
     * @return string
     */
    private function getRedirectUrl(Cooperation $cooperation, InputSource $inputSource): string
    {
        if (InputSource::COOPERATION_SHORT == $inputSource->short) {
            $url = route('cooperation.admin.cooperation.reports.index');
        } else {
            $scan = $cooperation->scans()->where('scans.short', '!=', Scan::EXPERT)->first();
            $url = route('cooperation.frontend.tool.simple-scan.my-plan.index', compact('scan')).'#download-section';
        }

        Log::debug($url);

        return $url;
    }

    /**
     * Get the file name for the filetype.
     *
     * @return mixed|string
     */
    private function getFileNameForFileType(FileType $fileType, User $user, InputSource $inputSource)
    {
        if ('pdf-report' == $fileType->short) {
            // 2013es14-Bewonster-A-g-Bewoner.pdf;
            $fileName = trim($user->building->postal_code).$user->building->number.'-'.Str::slug($user->getFullName()).'-'.$inputSource->name.'.pdf';
        } else {
            $fileName = (new FileTypeService($fileType))->niceFileName();
        }

        return $fileName;
    }
}
