<?php

namespace App\Jobs;

use App\Helpers\StepHelper;
use App\Models\BuildingInsulatedGlazing;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\User;
use App\Models\UserActionPlanAdviceComments;
use App\Scopes\GetValueScope;
use App\Services\DumpService;
use App\Services\UserActionPlanAdviceService;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     *
     * @param User $user
     * @param InputSource $inputSource
     * @param FileType $fileType
     * @param FileStorage $fileStorage
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
            \Log::debug(__CLASS__ . ' Is running in the console with a maximum execution time of: ' . ini_get('max_execution_time'));
        }

        $user = $this->user;
        $userCooperation = $this->user->cooperation;
        $inputSource = $this->inputSource;

        // load the buildingFeatures
        $building = $user->building()->with(['buildingFeatures' => function ($query) use ($inputSource) {
            $query->forInputSource($inputSource)->with('roofType', 'buildingType', 'energyLabel');
        }])->first();

        $buildingFeatures = $building->buildingFeatures;

        $GLOBALS['_cooperation'] = $userCooperation;
        $GLOBALS['_inputSource'] = $inputSource;

        $buildingInsulatedGlazings = BuildingInsulatedGlazing::where('building_id', $building->id)
            ->forInputSource($inputSource)
            ->with('measureApplication', 'insulatedGlazing', 'buildingHeating')
            ->get();

        // the comments that have been made on the action plan
        $userActionPlanAdviceComments = UserActionPlanAdviceComments::withoutGlobalScope(GetValueScope::class)
            ->where('user_id', $user->id)
            ->with('inputSource')
            ->get();

        $steps = $userCooperation->getActiveOrderedSteps();

        $userActionPlanAdvices = UserActionPlanAdviceService::getPersonalPlan($user, $inputSource);

        // we dont wat the actual advices, we have to show them in a different way
        $measures = UserActionPlanAdviceService::getCategorizedActionPlan($user, $inputSource, false);

        // full report for a user
        $reportForUser = DumpService::totalDump($user, $inputSource, false);

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
                if (!in_array('calculation', $tableData)) {
                    $reportData[$keys[0]][$keys[1]][implode('.', $tableData)] = $value;
                }
            }
        }

        // steps that are considered to be measures.
        $stepShorts = \DB::table('steps')
            ->where('short', '!=', 'general-data')
            ->select('short', 'id')
            ->get()
            ->pluck('short', 'id')
            ->flip()
            ->toArray();

        // retrieve all the comments by for each input source on a step
        $commentsByStep = StepHelper::getAllCommentsByStep($building);

        $noInterest = Interest::where('calculate_value', 4)->first();

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepShorts', 'commentsByStep', 'inputSource',
            'reportTranslations', 'reportData', 'userActionPlanAdvices', 'buildingFeatures', 'measures', 'calculations',
            'steps', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings', 'reportForUser', 'noInterest'
        ));

        // save the pdf report
        \Storage::disk('downloads')->put($this->fileStorage->filename, $pdf->output());

        $this->fileStorage->isProcessed();
    }

    public function failed(\Exception $exception)
    {
        $this->fileStorage->delete();
    }
}
