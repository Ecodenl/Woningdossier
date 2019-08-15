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
use App\Helpers\ToolHelper;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\buildingHeater;
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
     * @param  string  $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function byYear($filename = 'by-year')
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

    }

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

        foreach ($users as $key => $user) {
            /** @var Building $building */
            $building = $user->building;

            /** @var Collection $conversationRequestsForBuilding */
            $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope)
                                                             ->conversationRequestByBuildingId($building->id)
                                                             ->where('to_cooperation_id', $cooperation->id)->get();

            $createdAt           = optional($user->created_at)->format('Y-m-d');
            //$buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
            $buildingStatus = $building->status;
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


            // set alle the measures to the user
            foreach ($measures as $measure) {
                $row[$key][$measure->measure_name] = '';
            }

            // get the action plan advices for the user, but only for the resident his input source
            $userActionPlanAdvices = $user
                ->actionPlanAdvices()
                ->withOutGlobalScope(GetValueScope::class)
                ->residentInput()
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
                $buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
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
     * @param  Cooperation  $cooperation
     * @param bool $anonymized
     *
     * @return array
     */
    public static function totalReport(Cooperation $cooperation, bool $anonymized): array
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



        // build the header structure, we will set those in the csv and use it later on to get the answers from the users.
        // unfortunately we cant array dot the structure since we only need the labels
        foreach ($structure as $stepSlug => $stepStructure) {
            $step = Step::whereSlug($stepSlug)->first();
            foreach ($stepStructure as $tableWithColumnOrAndId => $contents) {
                if ($tableWithColumnOrAndId == 'calculations') {

                    // we will dot the array, map it so we can add the step name to it
                    $deeperContents = array_map(function ($content) use ($step) {
                        return $step->name.': '.$content;
                    }, \Illuminate\Support\Arr::dot($contents, $stepSlug.'.calculation.'));

                    $headers = array_merge($headers, $deeperContents);

                } else {
                    $headers[$stepSlug.'.'.$tableWithColumnOrAndId] = $step->name.': '.$contents['label'];
                }

            }
        }

        $rows[] = $headers;

        /**
         * Get the data for every user.
         * @var User $user
         */
        foreach ($users as $user) {
            // for each user we create a new row.
            $row = [];

            // collect basic info from a user.
            $building   = $user->building;
            $buildingId = $building->id;

            /** @var Collection $conversationRequestsForBuilding */
            $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope)
                                                             ->conversationRequestByBuildingId($building->id)
                                                             ->where('to_cooperation_id', $cooperation->id)->get();

            $createdAt           = optional($user->created_at)->format('Y-m-d');
            $buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
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
            $exampleBuilding = $building->exampleBuilding->isSpecific() ? $building->exampleBuilding->name : '';

            // set the personal userinfo
            if ($anonymized) {
                // set the personal userinfo
                $row[$building->id] = [
                    $createdAt, $buildingStatus, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding,
                ];
            } else {
                $row[$building->id] = [
                    $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                    $firstName, $lastName, $email, $phoneNumber,
                    $street, $number, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding,
                ];
            }

            $calculateData = static::getCalculateData($building, $user);

            // loop through the headers
            foreach ($headers as $tableWithColumnOrAndIdKey => $translatedInputName) {
                if (is_string($tableWithColumnOrAndIdKey)) {


                    // explode it so we can do stuff with it.
                    $tableWithColumnOrAndId = explode('.', $tableWithColumnOrAndIdKey);

                    // collect some basic info
                    // which will apply to (most) cases.
                    $step       = $tableWithColumnOrAndId[0];
                    $table      = $tableWithColumnOrAndId[1];
                    $columnOrId = $tableWithColumnOrAndId[2];

                    // determine what column we need to query on to get the results for the user.
                    /* @note this will work in most cases, if not the variable will be set again in a specific case. */
                    if (\Schema::hasColumn($table, 'building_id')) {
                        $whereUserOrBuildingId = [['building_id', '=', $buildingId]];
                    } else {
                        $whereUserOrBuildingId = [['user_id', '=', $user->id]];
                    }


                    // handle the calculation table.
                    // No its not a table, but we treat it as in the structure array.
                    if ($table == 'calculation') {
                        // works in most cases, otherwise they will be renamed etc.
                        $column      = $columnOrId;
                        $costsOrYear = $tableWithColumnOrAndId[3] ?? null;

                        switch ($step) {
                            case 'roof-insulation':
                                $roofCategory = $tableWithColumnOrAndId[2];
                                $column       = $tableWithColumnOrAndId[3];
                                $costsOrYear  = $tableWithColumnOrAndId[4] ?? null;

                                $calculationResult = is_null($costsOrYear) ? $calculateData['roof-insulation'][$roofCategory][$column] ?? '' : $calculateData['roof-insulation'][$roofCategory][$column][$costsOrYear] ?? '';
                                break;
                            default:
                                $calculationResult = is_null($costsOrYear) ? $calculateData[$step][$column] : $calculateData[$step][$column][$costsOrYear] ?? '';
                                break;
                        }

                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $calculationResult ?? '';
                    }

                    // handle the building_features table and its columns.
                    if ($table == 'building_features') {

                        $buildingFeature = BuildingFeature::withoutGlobalScope(GetValueScope::class)->where($whereUserOrBuildingId)->first();

                        if ($buildingFeature instanceof BuildingFeature) {

                            switch ($columnOrId) {
                                case 'roof_type_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->roofType instanceof RoofType ? $buildingFeature->roofType->name : '';
                                    break;
                                case 'building_type_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->buildingType->name ?? '';
                                    break;
                                case 'energy_label_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->energyLabel instanceof EnergyLabel ? $buildingFeature->energyLabel->name : '';
                                    break;
                                case 'facade_damaged_paintwork_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->damagedPaintwork instanceof FacadeDamagedPaintwork ? $buildingFeature->damagedPaintwork->name : '';
                                    break;
                                case 'facade_plastered_painted':
                                    $possibleAnswers = [
                                        1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                        2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                        3 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                    ];

                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->facade_plastered_painted] ?? '';
                                    break;
                                case 'facade_plastered_surface_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->plasteredSurface instanceof FacadePlasteredSurface ? $buildingFeature->plasteredSurface->name : '';
                                    break;
                                case 'monument':
                                    $possibleAnswers                              = [
                                        1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                        2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                        0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                    ];
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->monument] ?? '';
                                    break;
                                case 'wall_joints':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->wallJoints instanceof FacadeSurface ? $buildingFeature->wallJoints->name : '';
                                    break;
                                case 'contaminated_wall_joints':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->contaminatedWallJoints instanceof FacadeSurface ? $buildingFeature->contaminatedWallJoints->name : '';
                                    break;
                                default:
                                    // the column does not need a relationship, so just get the column
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->$columnOrId ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                    }

                    // handle the building_roof_types table and its columns.
                    if ($table == 'building_roof_types') {
                        $roofTypeId = $columnOrId;
                        $column     = $tableWithColumnOrAndId[3];

                        $buildingRoofType = BuildingRoofType::withoutGlobalScope(GetValueScope::class)
                                                            ->where('roof_type_id', $roofTypeId)
                                                            ->where($whereUserOrBuildingId)
                                                            ->first();

                        if ($buildingRoofType instanceof BuildingRoofType) {

                            switch ($column) {
                                case 'element_value_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->elementValue instanceof ElementValue ? $buildingRoofType->elementValue->value : '';
                                    break;
                                case 'building_heating_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->heating instanceof buildingHeater ? $buildingRoofType->heating->name : '';
                                    break;
                                case 'extra.measure_application_id':
                                    $extraIsArray                                 = is_array($buildingRoofType->extra);
                                    $measureApplicationId                         = $extraIsArray ? $buildingRoofType->extra['measure_application_id'] ?? null : null;
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = is_null($measureApplicationId) ? '' : MeasureApplication::find($measureApplicationId)->measure_name;
                                    break;
                                default:
                                    // check if we need to get data from the extra column
                                    if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                        $extraKey                                     = explode('extra.',
                                            $tableWithColumnOrAndIdKey)[1];
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->extra[$extraKey] ?? '';
                                    } else {
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->$column ?? '';
                                    }
                                    break;

                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                    }

                    // handle the user_interest table and its columns.
                    if (in_array($table, ['user_interest', 'user_interests'])) {
                        if ($step == 'insulated-glazing') {
                            $interestInType = 'measure_application';
                            $interestInId   = $tableWithColumnOrAndId[2];
                        } else {
                            $interestInType = $columnOrId;
                            $interestInId   = $tableWithColumnOrAndId[3];
                        }

                        $userInterest = UserInterest::withoutGlobalScope(GetValueScope::class)
                                                    ->where($whereUserOrBuildingId)
                                                    ->where('interested_in_id', $interestInId)
                                                    ->where('interested_in_type', $interestInType)
                                                    ->residentInput()->first();


                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $userInterest->interest->name ?? '';
                    }

                    // handle the element and service tables.
                    if (in_array($table, ['element', 'service'])) {
                        $whereUserOrBuildingId = [['building_id', '=', $buildingId]];
                        $elementOrServiceId    = $columnOrId;
                        switch ($table) {
                            case 'element':
                                /** @var BuildingElement $element */
                                $buildingElement = BuildingElement::withoutGlobalScope(GetValueScope::class)
                                                                  ->where($whereUserOrBuildingId)
                                                                  ->where('element_id', $elementOrServiceId)
                                                                  ->residentInput()->first();

                                if ($buildingElement instanceof BuildingElement) {
                                    // check if we need to get data from the extra column
                                    if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                        $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];

                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = is_array($buildingElement->extra) ? $buildingElement->extra[$extraKey] ?? '' : '';
                                    } else {
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingElement->elementValue->value ?? '';
                                    }
                                } else {
                                    // always set defaults
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                                }
                                break;
                            case 'service':

                                $buildingService = BuildingService::withoutGlobalScope(GetValueScope::class)
                                                                  ->where($whereUserOrBuildingId)
                                                                  ->where('service_id', $elementOrServiceId)
                                                                  ->residentInput()->first();
                                if ($buildingService instanceof BuildingService) {

                                    // check if we need to get data from the extra column
                                    if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                        $extraKey     = explode('extra.', $tableWithColumnOrAndIdKey)[1];
                                        $extraIsArray = is_array($buildingService->extra);

                                        // if is array, try to get the answer from the extra column, does the key not exist set a default value.
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $extraIsArray ? $buildingService->extra[$extraKey] ?? '' : '';
                                    } else {
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingService->serviceValue->value ?? '';
                                    }
                                } else {
                                    // always set defaults
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                                }

                        }
                    }

                    // handle the building_insulated_glazing table and its columns.
                    if ($table == 'building_insulated_glazings') {
                        $measureApplicationId = $columnOrId;
                        $column               = $tableWithColumnOrAndId[3];

                        /** @var BuildingInsulatedGlazing $buildingInsulatedGlazing */
                        $buildingInsulatedGlazing = BuildingInsulatedGlazing::withoutGlobalScope(GetValueScope::class)
                                                                            ->where($whereUserOrBuildingId)
                                                                            ->where('measure_application_id',
                                                                                $measureApplicationId)
                                                                            ->residentInput()->first();

                        if ($buildingInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                            switch ($column) {
                                case 'insulated_glazing_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->insulatedGlazing->name ?? '';
                                    break;
                                case 'building_heating_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->buildingHeating->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->$column ?? '';
                                    break;

                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                    }

                    // handle the building_pv_panels table and its column
                    if ($table == 'building_pv_panels') {
                        $column = $columnOrId;

                        /** @var BuildingPvPanel $buildingPvPanel */
                        $buildingPvPanel = BuildingPvPanel::withoutGlobalScope(GetValueScope::class)
                                                          ->where($whereUserOrBuildingId)
                                                          ->residentInput()->first();

                        if ($buildingPvPanel instanceof BuildingPvPanel) {
                            switch ($column) {
                                case 'pv_panel_orientation_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPvPanel->orientation->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPvPanel->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                    }

                    // handle the building_heaters table and its column
                    if ($table == 'building_heaters') {
                        $column = $columnOrId;

                        /** @var buildingHeater $buildingHeater */
                        $buildingHeater = BuildingHeater::withoutGlobalScope(GetValueScope::class)
                                                        ->where($whereUserOrBuildingId)
                                                        ->residentInput()->first();

                        if ($buildingHeater instanceof BuildingHeater) {
                            switch ($column) {
                                case 'pv_panel_orientation_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingHeater->orientation->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingHeater->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                    }

                    // handle the user_energy_habits table and its column
                    if ($table == 'user_energy_habits') {
                        $column = $columnOrId;

                        /** @var UserEnergyHabit $userEnergyHabit */
                        $userEnergyHabit = UserEnergyHabit::withoutGlobalScope(GetValueScope::class)
                                                          ->where($whereUserOrBuildingId)
                                                          ->residentInput()->first();

                        if ($userEnergyHabit instanceof UserEnergyHabit) {
                            switch ($column) {
                                case 'cook_gas':
                                    $radiobuttonsYesNo = [
                                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                                        2 => __('woningdossier.cooperation.radiobutton.no'),
                                    ];
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $radiobuttonsYesNo[$userEnergyHabit->cook_gas] ?? '';
                                    break;
                                case 'water_comfort_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->comfortLevelTapWater->name ?? '';
                                    break;
                                case 'heating_first_floor':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->heatingFirstFloor->name ?? '';
                                    break;
                                case 'heating_second_floor':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->heatingSecondFloor->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                    }

                    // handle the building_paintwork_statuses table and its column
                    if ($table == 'building_paintwork_statuses') {
                        $column = $columnOrId;

                        /** @var BuildingPaintworkStatus $buildingPaintworkStatus */
                        $buildingPaintworkStatus = BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)
                                                                          ->where($whereUserOrBuildingId)
                                                                          ->residentInput()->first();

                        if ($buildingPaintworkStatus instanceof BuildingPaintworkStatus) {
                            switch ($column) {
                                case 'paintwork_status_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->paintworkStatus->name ?? '';
                                    break;
                                case 'wood_rot_status_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->woodRotStatus->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                    }
                }
            }

            // no need to merge headers with the rows, we always set defaults so the count will always be the same.
            $rows[] = $row[$buildingId];
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
        $roofTypes = RoofType::all();
        $buildingRoofTypesArray = [];

        $selectedRoofTypes = $buildingRoofTypes->pluck('roof_type_id')->toArray();

        foreach($roofTypes->where('calculate_value', '<', 5) as $roofType) {
            $currentBuildingRoofType = $buildingRoofTypes->where('roof_type_id', $roofType->id)->first();
            $buildingRoofTypesArray[$roofType->short] = [
                'element_value_id' => $currentBuildingRoofType->element_value_id ?? null,
                'roof_surface' => $currentBuildingRoofType->roof_surface ?? null,
                'insulation_roof_surface' => $currentBuildingRoofType->insulation_roof_surface ?? null,
                'extra' => $currentBuildingRoofType->extra ?? null,
                'measure_application_id' => $currentBuildingRoofType->extra['measure_application_id'] ?? null,
                'building_heating_id' => $currentBuildingRoofType->building_heating_id ?? null,
            ];
        }

        // merge them
        $buildingRoofTypesArray = array_merge($selectedRoofTypes, $buildingRoofTypesArray);

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

        $roofInsulationSavings = RoofInsulation::calculate($building, $user, [
            'building_roof_types' => $buildingRoofTypesArray,
        ]);


        $highEfficiencyBoilerSavings = HighEfficiencyBoiler::calculate($building, $user, [
            'building_services' => $buildingBoilerArray,
            'habit' => [
                'amount_gas' => $userEnergyHabit->amount_gas ?? null,
            ],
        ]);

        $solarPanelSavings = SolarPanel::calculate($building, $user, [
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


}