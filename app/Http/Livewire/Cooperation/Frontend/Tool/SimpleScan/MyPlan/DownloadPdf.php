<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Jobs\PdfReport;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\User;
use App\Services\FileStorageService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Component;

// someday this will probably be refactored to a file storage download component.
class DownloadPdf extends Component
{
    use AuthorizesRequests;

    public FileType $fileType;
    public User $user;
    public InputSource $inputSource;
    public InputSource $masterInputSource;
    public Scan $scan;

    public bool $isFileBeingProcessed = false;
    public ?FileStorage $fileStorage;

    public function mount(User $user, Scan $scan)
    {
        /** @var FileType $fileType */
        $fileType = FileType::findByShort('pdf-report');
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $inputSource = HoomdossierSession::getInputSource(true);
        $this->fill(compact('scan', 'user', 'fileType', 'inputSource', 'masterInputSource'));

        $this->isFileBeingProcessed = $fileType->isBeingProcessed($user->building, $inputSource);
        $this->fileStorage = $fileType->files()->forBuilding($user->building)->forInputSource($inputSource)->first();

        // as this checks for file processing, there's a chance it isn't picked up by the queue,
        // so we check if it actually exists
        if ($this->fileStorage instanceof FileStorage) {
            $this->authorize('download', [$this->fileStorage, $user->building]);
        }
    }

    public function checkIfFileIsProcessed()
    {
        $this->isFileBeingProcessed = $this->fileType->isBeingProcessed($this->user->building, $this->inputSource);

        if (! $this->isFileBeingProcessed) {
            $this->fileStorage = $this->fileType->files()->forBuilding($this->user->building)->forInputSource($this->inputSource)->first();
        }
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.download-pdf');
    }

    /**
     * This method is responsible for generating the pdf.
     */
    public function generatePdf()
    {
        $this->isFileBeingProcessed = true;
        $this->fileStorage = null;

        abort_if($this->fileType->isBeingProcessed($this->user->building, $this->inputSource), 403);

        $this->handleExistingFiles();

        // and we create the new file
        $fileStorage = new FileStorage([
            'building_id' => $this->user->building->id,
            'cooperation_id' => $this->user->cooperation->id,
            'input_source_id' => $this->inputSource->id,
            'file_type_id' => $this->fileType->id,
            'filename' => $this->getFileNameForFileType(
                $this->fileType, $this->user, $this->inputSource
            ),
        ]);

        $this->authorize('store', [$fileStorage, $this->fileType]);

        $fileStorage->save();

        // so this could be more elegant with camelsnakekebakstrcase the crap out of it
        // that's for later.
        PdfReport::dispatch($this->user, $this->fileType, $fileStorage, $this->scan);
    }

    private function handleExistingFiles()
    {
        // this should also be expanded when used for more general purposes, check the FileStorageController for the logic on that.

        // get the existing files for the user and given file type
        $fileStorages = $this->fileType
            ->files()
            ->withExpired()
            ->forBuilding($this->user->building)
            ->forInputSource($this->inputSource)
            ->get();

        // delete them
        foreach ($fileStorages as $fileStorage) {
            FileStorageService::delete($fileStorage);
        }
    }

    private function getFileNameForFileType(FileType $fileType, User $user, InputSource $inputSource): string
    {
        // 1234AB11-Bewonster-A-g-Bewoner.pdf;
        return trim($user->building->postal_code).$user->building->number.'-'.Str::slug($user->getFullName()).'-'.$inputSource->name.'.pdf';
    }
}
