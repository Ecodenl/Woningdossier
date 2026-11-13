<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Jobs\Middleware\CheckLastResetAt;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\Scan;
use App\Models\User;
use App\Services\UserReport;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PdfReport extends NonHandleableJobAfterReset
{
    protected User $user;
    protected FileType $fileType;
    protected FileStorage $fileStorage;
    protected Scan $scan;

    /**
     * PdfReport constructor.
     */
    public function __construct(User $user, FileType $fileType, FileStorage $fileStorage, Scan $scan)
    {
        parent::__construct();
        $this->onQueue(Queue::EXPORTS);
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
        $this->user = $user;
        $this->scan = $scan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pdf = app(UserReport::class)->render($this->user, $this->scan);

        // save the pdf report
        Storage::disk('downloads')->put($this->fileStorage->filename, $pdf);

        $this->fileStorage->finishProcess();
    }

    public function failed(Throwable $exception)
    {
        $this->fileStorage->delete();

        report($exception);
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->user->building)];
    }
}
