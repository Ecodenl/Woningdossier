<?php

namespace App\Jobs;

use App\Helpers\HoomdossierSession;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\Hoomdossier;
use App\Helpers\StepHelper;
use App\Models\Cooperation;
use App\Services\DumpService;
use Barryvdh\DomPDF\Facade as PDF;

class PdfReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $anonymizeData;
    protected $fileType;
    protected $fileStorage;

    /**
     *
     * @param  User $user
     * @param  FileStorage $fileStorage
     * @param  FileType $fileType
     * @param  bool  $anonymizeData
     */
    public function __construct(User $user, FileType $fileType, FileStorage $fileStorage)
    {
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
        $this->user = $user;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (\App::runningInConsole()) {
            \Log::debug(__CLASS__.' Is running in the console with a maximum execution time of: '.ini_get('max_execution_time'));
        }

        $user = $this->user->load(['motivations']);

        $building = $user->building;
        // temporary session to get the right data for the dumb.
        $residentInputSource = InputSource::findByShort('resident');
        HoomdossierSession::setInputSource($residentInputSource);
        HoomdossierSession::setInputSourceValue($residentInputSource);
        HoomdossierSession::setBuilding($building);

        $buildingFeatures = $building->buildingFeatures;

        $GLOBALS['_cooperation'] = $user->cooperation;

        $inputSource = InputSource::findByShort('resident');

        $userActionPlanAdvices = UserActionPlanAdvice::getPersonalPlan($user, $inputSource);

        $advices = UserActionPlanAdvice::getCategorizedActionPlan($user, $inputSource);

        // full report for a user
        $reportForUser = DumpService::totalDump($user, false);

        // the translations for the columns / tables in the user data
        $reportTranslations = $reportForUser['translations-for-columns'];

        // undot it so we can handle the data in view later on
        $reportData = \App\Helpers\Arr::arrayUndot($reportForUser['user-data']);

        // steps that are considered to be measures.
        $stepSlugs = \DB::table('steps')
            ->where('slug', '!=', 'building-detail')
            ->where('slug', '!=', 'general-data')
            ->select('slug', 'id')
            ->get()
            ->pluck('slug', 'id')
            ->flip()
            ->toArray();

        // retrieve all the comments by for each input source on a step
        $commentsByStep = StepHelper::getAllCommentsByStep();
        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'cooperation', 'pdfData', 'stepSlugs',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices',
            'buildingFeatures', 'advices'
        ));


        // save the pdf report
        \Storage::disk('downloads')->put($this->fileStorage->filename, $pdf->output());

        \Session::forget('hoomdossier_session');

        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }

}
