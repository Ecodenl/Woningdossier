<?php

namespace App\Http\Controllers\Cooperation\Pdf;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
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
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
{
    /**
     * TESTING only, turn on the routes to use it.
     */
    public function index(Cooperation $userCooperation)
    {
        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;
        // Always retrieve from master
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $headers = DumpService::getStructureForTotalDumpService(false, false);

        $user = UserService::eagerLoadUserData($user, $inputSource);

        $building = $user->building;

        $buildingFeatures = $building->buildingFeatures;

        $GLOBALS['_cooperation'] = $userCooperation;
        $GLOBALS['_inputSource'] = $inputSource;

        $buildingInsulatedGlazings = $building->currentInsulatedGlazing->load('measureApplication', 'insulatedGlazing', 'buildingHeating');

        $userEnergyHabit = $user->energyHabit()->forInputSource($inputSource)->first();

        // unfortunately we can't load the whereHasMorph
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

        $userActionPlanAdvices = $userActionPlanAdvicesForCustomMeasureApplications->merge($remainingUserActionPlanAdvices)->sortBy('order');

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
                if (!in_array('calculation', $tableData)) {
                    $reportData[$keys[0]][$keys[1]][implode('.', $tableData)] = $value;
                }
            }
        }

        // Because the PDF will change we will just fuck the shit out of this old report
        unset($reportData['high-efficiency-boiler']);
        unset($reportData['heater']);
//dd($reportTranslations);

        $newSituation = [
            'heating' => [
                'Het huidig verbruik' => [
                    'amount-gas', 'amount-electricity',
                ],
                'Hoe wordt de warmte in de nieuwe situatie opgewekt' => [
                    'new-water-comfort', 'new-heat-source', 'new-heat-source-warm-tap-water',
                    'new-building-heating-application',
                ],
            ],
            'high-efficiency-boiler' => [
                [
                    'hr-boiler-replace', 'new-boiler-type',
                ],
                'Indicatie over kosten en baten van de cv-ketel' => [
                    'hr-boiler.amount_gas', 'hr-boiler.savings_gas', 'hr-boiler.savings_co2', 'hr-boiler.replace_year',
                    'hr-boiler.cost_indication', 'hr-boiler.interest_comparable',
                ],
                'Toelichting op de cv-ketel' => [
                    'hr-boiler-comment-coach', 'hr-boiler-comment-resident',
                ],
            ],
            'heat-pump' => [
                'Gegevens van de nieuwe warmtepomp' => [
                    'new-heat-pump-type', 'new-boiler-setting-comfort-heat', 'heat-pump.advised_system.required_power',
                    'heat-pump-preferred-power', 'new-cook-type', 'outside-unit-space', 'inside-unit-space',
                ],
                'Indicatie voor de efficiëntie van de warmtepomp' => [
                    'heat-pump.advised_system.share_heating', 'heat-pump.advised_system.share_tap_water',
                    'heat-pump.advised_system.scop_heating', 'heat-pump.advised_system.scop_tap_water',
                ],
                'Indicatie voor kosten en baten van de warmtepomp' => [
                    'heat-pump.savings_gas', 'heat-pump.savings_co2', 'heat-pump.savings_money',
                    'heat-pump.extra_consumption_electricity', 'heat-pump.cost_indication',
                    'heat-pump.interest_comparable',
                ],
                'Toelichting op de warmtepomp' => [
                    'heat-pump-comment-coach', 'heat-pump-comment-resident',
                ],
            ],
            'heater' => [
                'Geschat huidig verbruik' => [
                    'sun-boiler.consumption.water', 'sun-boiler.consumption.gas',
                ],
                'Specificaties systeem' => [
                    'sun-boiler.specs.size_boiler', 'sun-boiler.specs.size_collector',
                    'heater-pv-panel-orientation', 'heater-pv-panel-angle',
                ],
                'Indicatie voor kosten en baten van de zonneboiler' => [
                    'sun-boiler.production_heat', 'sun-boiler.percentage_consumption', 'sun-boiler.savings_gas',
                    'sun-boiler.savings_co2', 'sun-boiler.savings_money', 'sun-boiler.cost_indication',
                    'sun-boiler.interest_comparable',
                ],
                'Toelichting op de zonneboiler' => [
                    'sun-boiler-comment-coach', 'sun-boiler-comment-resident',
                ],
            ],
        ];




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

//        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepShorts', 'inputSource', 'userEnergyHabit', 'connectedCoachNames',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices', 'reportForUser', 'noInterest',
            'buildingFeatures', 'measures', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings', 'calculations'
        ));
//
        return $pdf->stream();

        return view('cooperation.pdf.user-report.index', compact(
            'user', 'building', 'userCooperation', 'stepShorts', 'inputSource', 'connectedCoachNames', 'userEnergyHabit',
            'commentsByStep', 'reportTranslations', 'reportData', 'userActionPlanAdvices', 'reportForUser', 'noInterest',
            'buildingFeatures', 'measures', 'userActionPlanAdviceComments', 'buildingInsulatedGlazings', 'calculations'
        ));
    }
}
