<?php

namespace App\Jobs;

use App\Helpers\StepHelper;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserActionPlanAdviceComments;
use App\Services\BuildingCoachStatusService;
use App\Services\DumpService;
use App\Services\UserActionPlanAdviceService;
use App\Services\UserService;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PdfReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;
    protected $inputSource;
    protected $anonymizeData;
    protected $fileType;
    protected $fileStorage;

    /**
     * PdfReport constructor.
     */
    public function __construct(User $user, InputSource $inputSource, FileType $fileType, FileStorage $fileStorage)
    {
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
        $this->inputSource = $inputSource;
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

        $user = $this->user;
        $userCooperation = $this->user->cooperation;
//        $inputSource = $this->inputSource;
        // Always retrieve from master
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $headers = DumpService::getStructureForTotalDumpService(false, false);

        $user = UserService::eagerLoadUserData($user, $inputSource);

        $building = $user->building;

        $buildingFeatures = $building->buildingFeatures;

        $GLOBALS['_cooperation'] = $userCooperation;
        $GLOBALS['_inputSource'] = $inputSource;

        $buildingInsulatedGlazings = $building->currentInsulatedGlazing->load('measureApplication', 'insulatedGlazing', 'buildingHeating');

        $steps = $userCooperation
            ->steps()
            ->withGeneralData()
            ->where('steps.parent_id', '=', null)
            ->orderBy('cooperation_steps.order')
            ->where('cooperation_steps.is_active', '1')
            ->get();

        $userEnergyHabit = $user->energyHabit()->forInputSource($inputSource)->first();


        // unfortunately we cant load the whereHasMorph
        // so we have to do 2 separate queries and merge the collections together.
        $userActionPlanAdvicesForCustomMeasureApplications = $user
            ->actionPlanAdvices()
            ->forInputSource($inputSource)
            ->whereIn('category', [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER])
            ->whereHasMorph(
                'userActionPlanAdvisable',
                [CustomMeasureApplication::class],
                function ($query) use ($inputSource) {
                    $query
                        ->forInputSource($inputSource);
                })->with(['userActionPlanAdvisable' => fn($query) => $query->forInputSource($inputSource)])->get();

        $remainingUserActionPlanAdvices = $user
            ->actionPlanAdvices()
            ->forInputSource($inputSource)
            ->whereIn('category', [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER])
            ->whereHasMorph(
                'userActionPlanAdvisable',
                [MeasureApplication::class, CooperationMeasureApplication::class]
            )->get();

        $userActionPlanAdvices = $userActionPlanAdvicesForCustomMeasureApplications->merge($remainingUserActionPlanAdvices);


        // we don't want the actual advices, we have to show them in a different way
        $measures = UserActionPlanAdviceService::getCategorizedActionPlan($user, $inputSource, false);

        // full report for a user
        $reportForUser = DumpService::totalDump($headers, $userCooperation, $user, $inputSource, false, true, true);

        // the translations for the columns / tables in the user data
        $reportTranslations = $reportForUser['translations-for-columns'];

        $calculations = $reportForUser['calculations'];
        $reportData = [];

        foreach ($reportForUser['user-data'] as $key => $value) {
            // so we now its a step.
            if (is_string($key)) {
                $keys = explode('.', $key);

                $tableData = array_splice($keys, 2);

                // we dont want the calculations in the report data, we need them separate
                if (! in_array('calculation', $tableData)) {
                    $reportData[$keys[0]][$keys[1]][implode('.', $tableData)] = $value;
                }
            }
        }

        // intersect the data, we don't need the data we won't show anyway
        $activeOrderedStepShorts = $steps->pluck('short')->flip()->toArray();
        $reportData = array_intersect_key($reportData, $activeOrderedStepShorts);

        // steps that are considered to be measures.
        $stepShorts = DB::table('steps')
            ->where('short', '!=', 'general-data')
            ->select('short', 'id')
            ->get()
            ->pluck('short', 'id')
            ->flip()
            ->toArray();

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = [];
        foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
            array_push($connectedCoachNames, User::find($coachId)->getFullName());
        }

        // retrieve all the comments by for each input source on a step
        $commentsByStep = StepHelper::getAllCommentsByStep($building);

        // the comments that have been made on the action plan
        $userActionPlanAdviceComments = UserActionPlanAdviceComments::forMe($user)
            ->with('inputSource')
            ->get()
            ->pluck('comment', 'inputSource.name')
            ->toArray();

        $noInterest = Interest::where('calculate_value', 4)->first();

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepShorts', 'inputSource', 'userEnergyHabit', 'connectedCoachNames',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices', 'reportForUser', 'noInterest',
            'buildingFeatures', 'measures', 'steps', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings', 'calculations'
        ));

        // save the pdf report
        Storage::disk('downloads')->put($this->fileStorage->filename, $pdf->output());

        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }
}
