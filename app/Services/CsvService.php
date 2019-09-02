<?php

namespace App\Services;

use App\Calculations\FloorInsulation;
use App\Calculations\Heater;
use App\Calculations\HighEfficiencyBoiler;
use App\Calculations\InsulatedGlazing;
use App\Calculations\RoofInsulation;
use App\Calculations\SolarPanel;
use App\Calculations\WallInsulation;
use App\Helpers\Arr;
use App\Helpers\FileFormats\CsvHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\ToolHelper;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\buildingHeater;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\CooperationScope;
use App\Scopes\GetValueScope;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CsvService
{
    /**
     * CSV Report that returns the measures by year, not used anymore. Its just here in case
     *
     * @deprecated
     *
     * @param  string  $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    /*public static function byYear($filename = 'by-year')
    {
        // get user data
        $user        = \Auth::user();
        $cooperation = $user->cooperations()->first();

        // get the users from the cooperations
        $users = $cooperation->users()->whereHas('buildings')->get();

        // set the csv headers
        $csvHeaders = [
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.country-code'),
        ];

        // put the measures inside the header array
        $thisYear = Carbon::now()->year;
        for ($startYear = $thisYear; $startYear <= ($thisYear + 100); ++$startYear) {
            $csvHeaders[] = $startYear;
        }

        $allUserMeasures = [];
        // new array for the userdata
        $rows = [];

        // since we only want the reports from the resident
        $residentInputSource = InputSource::findByShort('resident');

        foreach ($users as $key => $user) {

            $building = $user->building;

            $street      = $building->street;
            $number      = $building->number;
            $city        = $building->city;
            $postalCode  = $building->postal_code;
            $countryCode = $building->country_code;

            $firstName    = $user->first_name;
            $lastName     = $user->last_name;
            $email        = $user->account->email;
            $phoneNumber  = CsvHelper::escapeLeadingZero($user->phone_number);

            // set the personal userinfo
            $row[$key] = [
                $firstName, $lastName, $email, $phoneNumber, $street, $number, $city, $postalCode,
                $countryCode,
            ];

            // set all the years in range
            for ($startYear = $thisYear; $startYear <= ($thisYear + 100); ++$startYear) {
                $row[$key][$startYear] = '';
            }

            // get the action plan advices for the user, but only for the resident his input source
            $userActionPlanAdvices = $user
                ->actionPlanAdvices()
                ->withOutGlobalScope(GetValueScope::class)
                ->where('input_source_id', $residentInputSource->id)
                ->get();

            // get the user measures / advices
            foreach ($userActionPlanAdvices as $actionPlanAdvice) {
                $plannedYear = null == $actionPlanAdvice->planned_year ? $actionPlanAdvice->year : $actionPlanAdvice->planned_year;
                $measureName = $actionPlanAdvice->measureApplication->measure_name;

                if (is_null($plannedYear)) {
                    $plannedYear = $actionPlanAdvice->getAdviceYear($residentInputSource);
                }

                // create a new array with the measures for the user connected to the planned year
                $allUserMeasures[$plannedYear][] = $measureName;
            }

            // loop through the user measures and add them to the row
            foreach ($allUserMeasures as $year => $userMeasures) {
                $row[$key][$year] = implode(', ', $userMeasures);
            }

            $rows = $row;
        }

    }*/

    /**
     * CSV Report that returns the measures with year with full address data
     *
     * @param $cooperation
     * @param $anonymize
     *
     * @return array
     */
    public static function byMeasure($cooperation, $anonymize): array
    {
        // get the users from the cooperations
        $users = $cooperation->users()->whereHas('buildings')->get();

        if ($anonymize) {
            $csvHeaders = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        } else {
            $csvHeaders = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        }

        // get all the measures ordered by step
        $measures = MeasureApplication::leftJoin('steps', 'measure_applications.step_id', '=', 'steps.id')
            ->orderBy('steps.order')
            ->orderBy('measure_applications.measure_type')
            ->select(['measure_applications.*'])
            ->get();

        // put the measures inside the header array
        foreach ($measures as $measure) {
            $csvHeaders[] = $measure->measure_name;
        }

        // new array for the userdata
        $rows = [];

        // since we only want the reports from the resident
        $residentInputSource = InputSource::findByShort('resident');

        /**
         * @var User $user
         */
        foreach ($users as $key => $user) {
            /** @var Building $building */
            $building = $user->building;

            /** @var Collection $conversationRequestsForBuilding */
            $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope)
                                                             ->conversationRequestByBuildingId($building->id)
                                                             ->where('to_cooperation_id', $cooperation->id)->get();

            $createdAt           = optional($user->created_at)->format('Y-m-d');
            //$buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
            $buildingStatus = $building->getMostRecentBuildingStatus()->status->name;
            $allowAccess         = $conversationRequestsForBuilding->contains('allow_access', true) ? 'Ja' : 'Nee';
            $connectedCoaches    = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
            $connectedCoachNames = [];
            // get the names from the coaches and add them to a array
            foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
                array_push($connectedCoachNames, User::find($coachId)->getFullName());
            }
            // implode it.
            $connectedCoachNames = implode($connectedCoachNames, ', ');

            $firstName    = $user->first_name;
            $lastName     = $user->last_name;
            $email        = $user->account->email;
            $phoneNumber  = CsvHelper::escapeLeadingZero($user->phone_number);

            $street     = $building->street;
            $number     = $building->number;
            $city       = $building->city;
            $postalCode = $building->postal_code;

            // get the building features from the resident
            $buildingFeatures = $building
                ->buildingFeatures()
                ->withoutGlobalScope(GetValueScope::class)
                ->residentInput()
                ->first();



            $buildingType    = $buildingFeatures->buildingType->name ?? '';
            $buildYear       = $buildingFeatures->build_year ?? '';
            $exampleBuilding = optional($building->exampleBuilding)->isSpecific() ? $building->exampleBuilding->name : '';

            if ($anonymize) {
                // set the personal userinfo
                $row[$key] = [
                    $createdAt, $buildingStatus, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding,
                ];
            } else {
                // set the personal userinfo
                $row[$key] = [
                    $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                    $firstName, $lastName, $email, $phoneNumber,
                    $street, $number, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding,
                ];
            }


            // Set a default: all measures to empty
            foreach ($measures as $measure) {
                $row[$key][$measure->measure_name] = '';
            }

            // get the action plan advices for the user, but only for the resident his input source
            $userActionPlanAdvices = $user
                ->actionPlanAdvices()
                ->withOutGlobalScope(GetValueScope::class)
                ->residentInput()
                ->leftJoin('measure_applications', 'user_action_plan_advices.measure_application_id', '=', 'measure_applications.id')
                ->leftJoin('steps', 'measure_applications.step_id', '=', 'steps.id')
                ->orderBy('steps.order')
                ->orderBy('measure_applications.measure_type')
                ->select(['user_action_plan_advices.*'])
                ->get();

            // get the user measures / advices
            foreach ($userActionPlanAdvices as $actionPlanAdvice) {
                $plannedYear = null == $actionPlanAdvice->planned_year ? $actionPlanAdvice->year : $actionPlanAdvice->planned_year;
                $measureName = $actionPlanAdvice->measureApplication->measure_name;

                if (is_null($plannedYear)) {
                    $plannedYear = $actionPlanAdvice->getAdviceYear($residentInputSource);
                }

                // fill the measure with the planned year
                $row[$key][$measureName] = $plannedYear;
            }

            $rows = $row;
        }

        array_unshift($rows, $csvHeaders);

        return $rows;
    }


    /**
     * CSV Report that returns the questionnaire results
     *
     * @param  Cooperation  $cooperation
     * @param  bool  $anonymize
     *
     * @return array
     */
    public static function questionnaireResults(Cooperation $cooperation, bool $anonymize): array
    {
        $questionnaires = Questionnaire::withoutGlobalScope(new CooperationScope)
                                       ->where('cooperation_id', $cooperation->id)
                                       ->get();
        $rows           = [];

        if ($anonymize) {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
            ];
        } else {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
            ];
        }


        // get the users from the current cooperation that have the resident role
        $usersFromCooperation = $cooperation->getUsersWithRole(Role::findByName('resident'));

        /** @var User $user */
        foreach ($usersFromCooperation as $user) {
            $building = $user->building;
            if ($building instanceof Building && $user->hasRole('resident', $cooperation->id)) {

                /** @var Collection $conversationRequestsForBuilding */
                $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope)
                                                                 ->conversationRequestByBuildingId($building->id)
                                                                 ->where('to_cooperation_id', $cooperation->id)->get();

                $createdAt           = optional($user->created_at)->format('Y-m-d');
                //$buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
                $buildingStatus = $building->getMostRecentBuildingStatus()->status->name;
                $allowAccess         = $conversationRequestsForBuilding->contains('allow_access', true) ? 'Ja' : 'Nee';
                $connectedCoaches    = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
                $connectedCoachNames = [];
                // get the names from the coaches and add them to a array
                foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
                    array_push($connectedCoachNames, User::find($coachId)->getFullName());
                }
                // implode it.
                $connectedCoachNames = implode($connectedCoachNames, ', ');

                $firstName    = $user->first_name;
                $lastName     = $user->last_name;
                $email        = $user->account->email;
                $phoneNumber  = CsvHelper::escapeLeadingZero($user->phone_number);

                $street     = $building->street;
                $number     = $building->number;
                $city       = $building->city;
                $postalCode = $building->postal_code;

                // get the building features from the resident
                $buildingFeatures = $building
                    ->buildingFeatures()
                    ->withoutGlobalScope(GetValueScope::class)
                    ->residentInput()
                    ->first();

                $buildingType = $buildingFeatures->buildingType->name ?? '';
                $buildYear    = $buildingFeatures->build_year ?? '';

                // set the personal user info only if the user has question answers.
                if ($building->questionAnswers()->withoutGlobalScope(GetValueScope::class)->residentInput()->count() > 0) {
                    if ($anonymize) {
                        $rows[$building->id] = [
                            $createdAt, $buildingStatus, $allowAccess, $postalCode, $city,
                            $buildingType, $buildYear,
                        ];
                    } else {

                        $rows[$building->id] = [
                            $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                            $firstName, $lastName, $email, $phoneNumber,
                            $street, $number, $postalCode, $city,
                            $buildingType, $buildYear,
                        ];
                    }
                }
                foreach ($questionnaires as $questionnaire) {

                    $questionAnswersForCurrentQuestionnaire =
                        \DB::table('questionnaires')
                           ->where('questionnaires.id', $questionnaire->id)
                           ->join('questions', 'questionnaires.id', '=', 'questions.questionnaire_id')
                           ->leftJoin('translations', function ($leftJoin) {
                               $leftJoin->on('questions.name', '=', 'translations.key')
                                        ->where('language', '=', app()->getLocale());
                           })
                           ->leftJoin('questions_answers',
                               function ($leftJoin) use ($building) {
                                   $leftJoin->on('questions.id', '=', 'questions_answers.question_id')
                                            ->where('questions_answers.building_id', '=', $building->id);
                               })
                           ->select('questions_answers.answer', 'questions.id as question_id',
                               'translations.translation as question_name')
                           ->get();


                    // loop through the answers for ONE questionnaire
                    foreach ($questionAnswersForCurrentQuestionnaire as $questionAnswerForCurrentQuestionnaire) {
                        $answer          = $questionAnswerForCurrentQuestionnaire->answer;
                        $currentQuestion = Question::withTrashed()->find($questionAnswerForCurrentQuestionnaire->question_id);

                        // check if the question
                        if ($currentQuestion instanceof Question) {
                            // if the question has options, we have to get the translations from that table otherwise there would be ids in the csv
                            if ($currentQuestion->hasQuestionOptions()) {
                                $questionOptionAnswer = [];

                                // explode on array since some questions are multi select
                                $explodedAnswers = explode('|', $answer);

                                foreach ($explodedAnswers as $explodedAnswer) {
                                    // check if the current question has options
                                    // the question can contain a int but can be a answer to a question like "How old are you"
                                    if ($currentQuestion->hasQuestionOptions() && ! empty($explodedAnswer)) {
                                        $questionOption = QuestionOption::find($explodedAnswer);
                                        array_push($questionOptionAnswer, $questionOption->name);
                                    }
                                }

                                // the questionOptionAnswer can be empty if the the if statements did not pass
                                // so we check that before assigning it.
                                if ( ! empty($questionOptionAnswer)) {
                                    // implode it
                                    $answer = implode($questionOptionAnswer, '|');
                                }
                            }
                            // set the question name in the headers
                            // yes this overwrites it all the time, but thats the point.
                            $headers[$questionAnswerForCurrentQuestionnaire->question_name] = $questionAnswerForCurrentQuestionnaire->question_name;

                            // set the question answer
                            $rows[$building->id][] = $answer;
                        }
                    }
                }
            }
        }

        // unset the whole empty arrays
        // so we only set rows with answers.
        foreach ($rows as $buildingId => $row) {
            if (Arr::isWholeArrayEmpty($row)) {
                unset($rows[$buildingId]);
            }
        }

        array_unshift($rows, $headers);

        return $rows;
    }


    /**
     * Get the year from the action plan advice
     *
     * @param  UserActionPlanAdvice  $actionPlanAdvice
     *
     * @return int
     */
    public static function getYear(UserActionPlanAdvice $actionPlanAdvice): int
    {
        $residentInputSource = InputSource::findByShort('resident');

        // try to obtain the years from the action plan
        $plannedYear = $actionPlanAdvice->planned_year ?? $actionPlanAdvice->year;

        // set the year and if null get the advice year
        $year = $plannedYear ?? $actionPlanAdvice->getAdviceYear($residentInputSource);

        return $year;
    }

    /**
     * Get the total report for all users by the cooperation
     *
     * @param Cooperation $cooperation
     * @param InputSource $inputSource
     * @param bool $anonymized
     * @return array
     */
    public static function totalReport(Cooperation $cooperation, InputSource $inputSource, bool $anonymized): array
    {
        $users = $cooperation->users()->whereHas('buildings')->get();

        if ($anonymized) {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        } else {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        }

        // get the content structure of the whole tool.
        $structure = ToolHelper::getToolStructure();

        $leaveOutTheseDuplicates = [
            // hoofddak
            'roof-insulation.building_features.roof_type_id',
            // bewoners, gasverbruik en type ketel
            'high-efficiency-boiler.user_energy_habits.resident_count',
            'high-efficiency-boiler.user_energy_habits.amount_gas',
            'high-efficiency-boiler.service.5.service_value_id',
            // elektriciteitsverbruik
            'solar-panels.user_energy_habits.amount_electricity',
            // comfort niveau
            'heater.user_energy_habits.water_comfort_id',
            'heater.calculation.production_heat.help',
        ];

        // build the header structure, we will set those in the csv and use it later on to get the answers from the users.
        // unfortunately we cant array dot the structure since we only need the labels
        foreach ($structure as $stepSlug => $stepStructure) {
            // building-detail contains data that is already present in the columns above
            if (!in_array($stepSlug, ['building-detail'])) {
                $step = Step::whereSlug($stepSlug)->first();
                foreach ($stepStructure as $tableWithColumnOrAndId => $contents) {
                    if ($tableWithColumnOrAndId == 'calculations') {

                        // If you want to go ahead and translate in a different namespace, do it here
                        // we will dot the array, map it so we can add the step name to it
                        $deeperContents = array_map(function ($content) use ($step) {
                            return $step->name.': '.$content;
                        }, \Illuminate\Support\Arr::dot($contents, $stepSlug.'.calculation.'));

                        $headers = array_merge($headers, $deeperContents);

                    } else {
                        $headers[$stepSlug.'.'.$tableWithColumnOrAndId] = $step->name.': '.str_replace([
                            '&euro;', '€'
                            ], ['euro', 'euro'], $contents['label']);
                    }
                }
            }
        }

        foreach($leaveOutTheseDuplicates as $leaveOut){
            unset($headers[$leaveOut]);
        }

        $rows[] = $headers;

        /**
         * Get the data for every user.
         * @var User $user
         */
        foreach ($users as $user) {
            $rows[$user->building->id] = DumpService::totalDump($user, $inputSource, $anonymized, false)['user-data'];
        }

        return $rows;
    }

    /**
     * Return the calculate data for each step, returns it in the format how the calculate classes expects it.
     *
     * @param  Building  $building
     * @param  User  $user
     *
     * @return array
     */
    public static function getCalculateData(Building $building, User $user): array
    {
        // collect some info about their building
        /** @var BuildingFeature $buildingFeature */
        $buildingFeature   = $building->buildingFeatures()->withoutGlobalScope(GetValueScope::class)->residentInput()->first();
        $buildingElements = $building->buildingElements()->withoutGlobalScope(GetValueScope::class)->residentInput()->get();
        $buildingPaintworkStatus = $building->currentPaintworkStatus()->withoutGlobalScope(GetValueScope::class)->residentInput()->first();
        $buildingRoofTypes = $building->roofTypes()->withoutGlobalScope(GetValueScope::class)->residentInput()->get();
        $buildingServices = $building->buildingServices()->withoutGlobalScope(GetValueScope::class)->residentInput()->get();
        $buildingPvPanels = $building->pvPanels()->withoutGlobalScope(GetValueScope::class)->residentInput()->first();
        $buildingHeater = $building->heater()->withoutGlobalScope(GetValueScope::class)->residentInput()->first();

        $userEnergyHabit = $user->energyHabit()->withoutGlobalScope(GetValueScope::class)->residentInput()->first();

        $wallInsulationElement = Element::where('short', 'wall-insulation')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();
        $frames = Element::where('short', 'frames')->first();
        $crackSealing = Element::where('short', 'crack-sealing')->first();
        $floorInsulationElement = Element::where('short', 'floor-insulation')->first();
        $crawlspaceElement = Element::where('short', 'crawlspace')->first();

        $boilerService = Service::where('short', 'boiler')->first();
        $solarPanelService = Service::where('short', 'total-sun-panels')->first();
        $heaterService = Service::where('short', 'sun-boiler')->first();

        // handle stuff for the wall insulation
        $wallInsulationBuildingElement = $buildingElements->where('element_id', $wallInsulationElement->id)->first();

        // handle the stuff for the insulated glazing
        // the user interest on the insulated glazing
        // key = measure_application_id
        // val = interest_id
        $userInterestsForInsulatedGlazing = $user
            ->interests()
            ->withoutGlobalScope(GetValueScope::class)
            ->residentInput()
            ->where('interested_in_type', 'measure_application')
            ->select('interested_in_id', 'interest_id')
            ->get()
            ->pluck('interest_id', 'interested_in_id')
            ->toArray();

        /** @var Collection $buildingInsulatedGlazings */
        $buildingInsulatedGlazings = $building
            ->currentInsulatedGlazing()
            ->withoutGlobalScope(GetValueScope::class)
            ->residentInput()
            ->select('measure_application_id', 'insulating_glazing_id', 'building_heating_id', 'm2', 'windows')
            ->get();


        // build the right structure for the calculation
        $buildingInsulatedGlazingArray = [];
        foreach ($buildingInsulatedGlazings as $buildingInsulatedGlazing) {
            $buildingInsulatedGlazingArray[$buildingInsulatedGlazing->measure_application_id] = [
                'insulating_glazing_id' => $buildingInsulatedGlazing->insulating_glazing_id,
                'building_heating_id'   => $buildingInsulatedGlazing->building_heating_id,
                'm2'                    => $buildingInsulatedGlazing->m2,
                'windows'               => $buildingInsulatedGlazing->windows,
            ];
        }


        // handle the wood / frame / crack sealing elements for the insulated glazing

        $buildingWoodElement = $buildingElements->where('element_id', $woodElements->id)->pluck('element_value_id')->toArray();
        $buildingElementsArray[$woodElements->short][$woodElements->id] = array_combine($buildingWoodElement, $buildingWoodElement) ?? null;

        $buildingFrameElement = $buildingElements->where('element_id', $frames->id)->first();
        $buildingElementsArray[$frames->id][$frames->short] = $buildingFrameElement->element_value_id ?? null;

        $buildingCrackSealingElement = $buildingElements->where('element_id', $crackSealing->id)->first();
        $buildingElementsArray[$crackSealing->id][$crackSealing->short] = $buildingCrackSealingElement->element_value_id ?? null;


        $buildingPaintworkStatusesArray = [
            'last_painted_year' => $buildingPaintworkStatus->last_painted_year ?? null,
            'paintwork_status_id' => $buildingPaintworkStatus->paintwork_status_id ?? null,
            'wood_rot_status_id' => $buildingPaintworkStatus->wood_rot_status_id ?? null,
        ];

        // handle the stuff for the floor insulation.
        $floorInsulationElementValueId = $buildingElements->where('element_id', $floorInsulationElement->id)->first()->element_value_id ?? null;
        $buildingCrawlspaceElement = $buildingElements->where('element_id', $crawlspaceElement->id)->first();

        $floorInsulationBuildingElements = [
            'crawlspace' => $buildingCrawlspaceElement->extra['has_crawlspace'] ?? null,
            $crawlspaceElement->id => [
                'extra' => $buildingCrawlspaceElement->extra['access'] ?? null,
                'element_value_id' => $buildingCrawlspaceElement->element_value_id ?? null,
            ],
        ];

        $floorBuildingFeatures = [
            'floor_surface' => $buildingFeature->floor_surface ?? null,
            'insulation_surface' => $buildingFeature->insulation_surface ?? null,
        ];

        // now lets handle the roof insulation stuff.
        $buildingRoofTypesArray = ['id' => []];

        /** @var BuildingRoofType $buildingRoofType */
        foreach($buildingRoofTypes as $buildingRoofType){
            $short = $buildingRoofType->roofType->short;
            $buildingRoofTypesArray[$short] = [
                'element_value_id' => $buildingRoofType->element_value_id,
                'roof_surface' => $buildingRoofType->roof_surface,
                'insulation_roof_surface' => $buildingRoofType->insulation_roof_surface,
                'extra' => $buildingRoofType->extra,
                'measure_application_id' => $buildingRoofType->extra['measure_application_id'] ?? null,
                'building_heating_id' => $buildingRoofType->building_heating_id,
            ];
            $buildingRoofTypesArray['id'][] = $buildingRoofType->roofType->id;

            // if the roof is a flat roof OR the tiles_condition is empty: remove it!!
            // this is needed as the tiles condition has a different type of calculation
            // than bitumen has
            if(array_key_exists('tiles_condition', $buildingRoofTypesArray[$short]['extra'])){
                if ($short == 'flat' || empty($buildingRoofTypesArray[$short]['extra']['tiles_condition'])){
                    unset($buildingRoofTypesArray[$short]['extra']['tiles_condition']);
                }
            }
        }

        // now we handle the hr boiler stuff
        $buildingBoilerService = $buildingServices->where('service_id', $boilerService->id)->first();

        $buildingBoilerArray = [
            $boilerService->id => [
                'service_value_id' => $buildingBoilerService->service_value_id ?? null,
                'extra' => $buildingBoilerService->extra['date'] ?? null,
            ],
        ];

        // handle the solar panel stuff.

        // get the user interests for the solar panels keyed by type
        $userInterestsForSolarPanels = $user
            ->interests()
            ->withoutGlobalScope(GetValueScope::class)
            ->residentInput()
            ->where('interested_in_type', 'service')
            ->where('interested_in_id', $solarPanelService->id)
            ->select('interested_in_id', 'interest_id', 'interested_in_type')
            ->get()
            ->keyBy('interested_in_type')->map(function ($item) {
                return [$item['interested_in_id'] => $item['interest_id']];
            })->toArray();

        // handle the heater stuff
        $userInterestsForHeater = $user
            ->interests()
            ->withoutGlobalScope(GetValueScope::class)
            ->residentInput()
            ->where('interested_in_type', 'service')
            ->where('interested_in_id', $heaterService->id)
            ->select('interested_in_id', 'interest_id', 'interested_in_type')
            ->get()
            ->keyBy('interested_in_type')->map(function ($item) {
                return [$item['interested_in_id'] => $item['interest_id']];
            })->toArray();

        $wallInsulationSavings = WallInsulation::calculate($building, $userEnergyHabit, [
            'cavity_wall'                 => $buildingFeature->cavity_wall ?? null,
            'element'                     => [$wallInsulationElement->id => $wallInsulationBuildingElement->element_value_id ?? null],
            'insulation_wall_surface'     => $buildingFeature->insulation_wall_surface ?? null,
            'wall_joints'                 => $buildingFeature->wall_joints ?? null,
            'contaminated_wall_joints'    => $buildingFeature->contaminated_wall_joints ?? null,
            'facade_plastered_painted'    => $buildingFeature->facade_plastered_painted ?? null,
            'facade_plastered_surface_id' => $buildingFeature->facade_plastered_surface_id ?? null,
            'facade_damaged_paintwork_id' => $buildingFeature->facade_damaged_paintwork_id ?? null,
        ]);


        $insulatedGlazingSavings = InsulatedGlazing::calculate($building, $user, [
            'user_interests' => $userInterestsForInsulatedGlazing,
            'building_insulated_glazings' => $buildingInsulatedGlazingArray,
            'building_elements' => $buildingElementsArray,
            'window_surface' => $buildingFeature->window_surface ?? null,
            'building_paintwork_statuses' => $buildingPaintworkStatusesArray,
        ]);

        $floorInsulationSavings = FloorInsulation::calculate($building, $user, [
            'element' => [$floorInsulationElement->id => $floorInsulationElementValueId],
            'building_elements' => $floorInsulationBuildingElements,
            'building_features' => $floorBuildingFeatures,
        ]);

        $roofInsulationSavings = RoofInsulation::calculate($building, $inputSource, [
            'building_roof_types' => $buildingRoofTypesArray,
        ]);

        $highEfficiencyBoilerSavings = HighEfficiencyBoiler::calculate($building, $inputSource, [
            'building_services' => $buildingBoilerArray,
            'habit' => [
                'amount_gas' => $userEnergyHabit->amount_gas ?? null,
            ],
        ]);

        $solarPanelSavings = SolarPanel::calculate($building, [
            'building_pv_panels' => $buildingPvPanels instanceOf BuildingPvPanel ? $buildingPvPanels->toArray() : [],
            'user_energy_habits' => [
                'amount_electricity' => $userEnergyHabit->amount_electricity ?? null,
            ],
            'interest' => $userInterestsForSolarPanels,
        ]);

        $heaterSavings = Heater::calculate($building, $user, [
            'building_heaters' => [
                $buildingHeater instanceof BuildingHeater ? $buildingHeater->toArray() : [],
            ],
            'user_energy_habits' => [
                'water_comfort_id' => $userEnergyHabit->water_comfort_id ?? null,
            ],
            'interest' => $userInterestsForHeater,
        ]);

        return [
            'wall-insulation' => $wallInsulationSavings,
            'insulated-glazing' => $insulatedGlazingSavings,
            'floor-insulation' => $floorInsulationSavings,
            'roof-insulation' => $roofInsulationSavings,
            'high-efficiency-boiler' => $highEfficiencyBoilerSavings,
            'solar-panels' => $solarPanelSavings,
            'heater' => $heaterSavings,
        ];
    }

    protected static function formatFieldOutput($column, $value, $maybe1, $maybe2){
        //dump("formatFieldOutput (" . $column . ", " . $value . ", " . $maybe1 . ", " . $maybe2 . ")");
        $decimals = 0;
        $shouldRound = false;

        if(self::isYear($column) || self::isYear($maybe1, $maybe2)){
            return $value;
        }

        if (!is_numeric($value)){
            return $value;
        }

        if (in_array($column, ['interest_comparable',])){
            $decimals = 1;
        }
        if ($column == 'specs' && $maybe1 == 'size_collector'){
            $decimals = 1;
        }
        if ($column == 'paintwork' && $maybe1 == 'costs'){
            /// round the cost for paintwork
            $shouldRound = true;
        }

        return self::formatOutput($column, $value, $decimals, $shouldRound);
    }

    /**
     * Format the output of the given column and value.
     *
     * @param string $column
     * @param mixed $value
     * @param  int  $decimals
     * @param  bool  $shouldRound
     *
     * @return float|int|string
     */
    protected static function formatOutput($column, $value, $decimals = 0, $shouldRound = false){
        //dump("formatOutput (" . $column . ", " . $value . ", " . $decimals . ", " . $shouldRound . ")");

        if (in_array($column, ['percentage_consumption',]) ||
            stristr($column, 'savings_') !== false ||
            stristr($column, 'cost')){
            $value = NumberFormatter::round($value);
        }
        if ($shouldRound){
            $value = NumberFormatter::round($value);
        }
        // We should let Excel do the separation of thousands
        return number_format($value, $decimals, ",", "");
        //return NumberFormatter::format($value, $decimals, $shouldRound);
    }

    protected static function translateExtraValueIfNeeded($value){
        if (in_array($value, ['yes', 'no', 'unknown'])){
            $key = 'general.options.%s.title';
            return Translation::translate(sprintf($key, $value));
        }
    }

    /**
     * Returns whether or not two (optional!) columns contain a year or not
     *
     * @param string $column
     * @param string $extraValue
     *
     * @return bool
     */
    protected static function isYear($column, $extraValue = ''){
        if (!is_null($column)){
            if (stristr($column, 'year') !== false){
                return true;
            }
            if ($column == 'extra'){
                return in_array($extraValue, [
                    'year',
                ]);
            }
        }

        return false;
    }

}