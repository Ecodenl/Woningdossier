<?php

namespace App\Services;

use App\Helpers\Arr;
use App\Helpers\Calculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\KeyFigures\Heater\KeyFigures as HeaterKeyFigures;
use App\Helpers\KeyFigures\PvPanels\KeyFigures as SolarPanelsKeyFigures;
use App\Helpers\KeyFigures\RoofInsulation\Temperature;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\InsulatingGlazing;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\PaintworkStatus;
use App\Models\PrivateMessage;
use App\Models\PvPanelOrientation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\WoodRotStatus;
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
        $users = $cooperation->users;

        // set the csv headers
        $csvHeaders = [
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.mobilenumber'),
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
            $building = $user->buildings()->first();
            if ($building instanceof Building) {
                $street      = $building->street;
                $number      = $building->number;
                $city        = $building->city;
                $postalCode  = $building->postal_code;
                $countryCode = $building->country_code;

                $firstName    = $user->first_name;
                $lastName     = $user->last_name;
                $email        = $user->email;
                $phoneNumber  = "'".$user->phone_number;
                $mobileNumber = $user->mobile;

                // set the personal userinfo
                $row[$key] = [
                    $firstName, $lastName, $email, $phoneNumber, $mobileNumber, $street, $number, $city, $postalCode,
                    $countryCode
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
            }

            $rows = $row;
        }

        $csv = static::write($csvHeaders, $rows);

        return static::export($csv, $filename);
    }

    /**
     * CSV Report that returns the measures with year with full address data
     *
     * @param  string  $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function byMeasure($filename = 'per-jaar-met-adres-gegevens')
    {
        // Get the current cooperation
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        // get the users from the cooperations
        $users = $cooperation->users;

        // set the csv headers
        $csvHeaders = [
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.mobilenumber'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
        ];

        // get all the measures
        $measures = MeasureApplication::all();

        // put the measures inside the header array
        foreach ($measures as $measure) {
            $csvHeaders[] = $measure->measure_name;
        }

        // new array for the userdata
        $rows = [];

        // since we only want the reports from the resident
        $residentInputSource = InputSource::findByShort('resident');

        foreach ($users as $key => $user) {
            $building = $user->buildings()->first();
            if ($building instanceof Building) {


                /** @var Collection $conversationRequestsForBuilding */
                $conversationRequestsForBuilding = PrivateMessage::conversationRequestByBuildingId($building->id)->forMyCooperation()->get();

                $createdAt           = $user->created_at;
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
                $email        = $user->email;
                $phoneNumber  = "'".$user->phone_number;
                $mobileNumber = $user->mobile;

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
                $exampleBuilding = $building->exampleBuilding->name ?? '';

                // set the personal userinfo
                $row[$key] = [
                    $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                    $firstName, $lastName, $email, $phoneNumber, $mobileNumber,
                    $street, $number, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding
                ];

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
            }
            $rows = $row;
        }

        $csv = static::write($csvHeaders, $rows);

        return static::export($csv, $filename);
    }

    /**
     * CSV Report that returns the years by measure with only the zipcode
     *
     * @param  string  $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function byMeasureAnonymized($filename = 'maatregelen-anoniem')
    {
        // Get the current cooperation
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        // get the users from the cooperations
        $users = $cooperation->users;

        // set the csv headers
        $csvHeaders = [
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
        ];

        // get all the measures
        $measures = MeasureApplication::all();

        // put the measures inside the header array
        foreach ($measures as $measure) {
            $csvHeaders[] = $measure->measure_name;
        }

        // new array for the userdata
        $rows = [];

        // since we only want the reports from the resident
        $residentInputSource = InputSource::findByShort('resident');

        foreach ($users as $key => $user) {
            $building = $user->buildings()->first();
            if ($building instanceof Building) {

                $createdAt      = $user->created_at;
                $buildingStatus = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);

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
                $exampleBuilding = $building->exampleBuilding->name ?? '';

                // set the personal userinfo
                $row[$key] = [
                    $createdAt, $buildingStatus,
                    $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding
                ];

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
            }
            $rows = $row;
        }

        $csv = static::write($csvHeaders, $rows);

        return static::export($csv, $filename);
    }

    /**
     * CSV Report that returns the questionnaire results
     *
     * @param  string  $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function questionnaireResults($filename = 'vragenlijst-met-adres-gegevens')
    {
        $questionnaires = Questionnaire::all();
        $rows           = [];

        $headers = [
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.mobilenumber'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
        ];

        // we only want to query on the buildings that belong to the cooperation of the current user
        $currentCooperation = Cooperation::find(HoomdossierSession::getCooperation());

        // get the users from the current cooperation that have the resident role
        $usersFromCooperation = $currentCooperation->users()->role('resident')->with('buildings')->get();


        foreach ($usersFromCooperation as $user) {
            $building = $user->buildings()->first();

            /** @var Collection $conversationRequestsForBuilding */
            $conversationRequestsForBuilding = PrivateMessage::conversationRequestByBuildingId($building->id)->forMyCooperation()->get();

            $createdAt           = $user->created_at;
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
            $email        = $user->email;
            $phoneNumber  = "'".$user->phone_number;
            $mobileNumber = $user->mobile;

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
                $rows[$building->id] = [
                    $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                    $firstName, $lastName, $email, $phoneNumber, $mobileNumber,
                    $street, $number, $postalCode, $city,
                    $buildingType, $buildYear
                ];
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

        // unset the whole empty arrays
        // so we only set rows with answers.
        foreach ($rows as $buildingId => $row) {
            if (Arr::isWholeArrayEmpty($row)) {
                unset($rows[$buildingId]);
            }
        }

        $csv = static::write($headers, $rows);

        return static::export($csv, $filename);

    }

    /**
     * CSV Report that returns the questionnaire results only with the zipcode and basic info
     *
     * @param  string  $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function questionnaireResultsAnonymized($filename = 'vragenlijst-anoniem')
    {
        $questionnaires = Questionnaire::all();
        $rows           = [];

        $headers = [
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
        ];

        // we only want to query on the buildings that belong to the cooperation of the current user
        $currentCooperation = Cooperation::find(HoomdossierSession::getCooperation());

        // get the users from the current cooperation that have the resident role
        $usersFromCooperation = $currentCooperation->users()->role('resident')->with('buildings')->get();


        foreach ($usersFromCooperation as $user) {
            $building = $user->buildings()->first();

            $createdAt      = $user->created_at;
            $buildingStatus = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
            $postalCode     = $building->postal_code;
            $city           = $building->city;

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
                $rows[$building->id] = [
                    $createdAt, $buildingStatus, $postalCode, $city,
                    $buildingType, $buildYear
                ];
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

        // unset the whole empty arrays
        // so we only set rows with answers.
        foreach ($rows as $buildingId => $row) {
            if (Arr::isWholeArrayEmpty($row)) {
                unset($rows[$buildingId]);
            }
        }

        $csv = static::write($headers, $rows);

        return static::export($csv, $filename);

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


    public static function totalDump($filename = 'totale-dump')
    {

        $residentInputSource = InputSource::findByShort('resident');

        // Get the current cooperation with its users
        $cooperation = Cooperation::with('users')->find(HoomdossierSession::getCooperation());

        // Get the users from the cooperations
        $users = $cooperation->users;

        // Get all the steps with its measure applications
        $steps = Step::with('measureApplications')->get();

        $headers = [];
        $rows    = [];

        // get the content structure of the whole tool.
        $structure = static::getContentStructure();

        dd($structure);
        foreach ($structure as $step => $stepStructure) {

            foreach ($stepStructure as $tableAndColumnName => $question) {
                $headers[$tableAndColumnName] = $question['label'];
            }
            // for every step we need to set measure application and saving header.
            $currentStep = Step::with('measureApplications')->where('slug', $step)->first();
            // check if the step has measures
            if ($currentStep->measureApplications->isNotEmpty()) {
                foreach ($currentStep->measureApplications as $measureApplication) {
//                    $headers[] = $measureApplication->measure_name;
                }
                // get the general cost stuff.
                foreach (__($step.'.costs') as $key => $translations) {
                    if ( ! empty($translations)) {
                        if (array_key_exists('title', $translations)) {
                            $headers[] = $translations['title'];
                        }
                    }
                }

            dd($headers);
            }
        }

//        foreach ($users as $user) {
//            $row = [];
//
//            // loop through the steps
//            foreach ($steps as $step) {
//                // get the measure applications
//                foreach ($step->measureApplications as $measureApplication) {
//
//
//                    // get the action plan advices for the user, but only for the resident his input source
//                    $userActionPlanAdvices = $user
//                        ->actionPlanAdvices()
//                        ->withOutGlobalScope(GetValueScope::class)
//                        ->residentInput()
//                        ->get();
//
//                    // get the user measures / advices
//                    foreach ($userActionPlanAdvices as $actionPlanAdvice) {
//                        $plannedYear = $actionPlanAdvice->planned_year ?? $actionPlanAdvice->year;
//                        $measureName = $actionPlanAdvice->measureApplication->measure_name;
//                        $co2Savings = Calculator::calculateCo2Savings($actionPlanAdvice->savings_gas);
//                        $savingsGas = $actionPlanAdvice->savings_gas;
//                        $moneySavings = $actionPlanAdvice->savings_money;
//
//                        if (is_null($plannedYear)) {
//                            $plannedYear = $actionPlanAdvice->getAdviceYear($residentInputSource);
//                        }
//
//                        // fill the measure with the planned year
//                        $row[$user->id][$step->slug][$measureApplication->measure_name] = $plannedYear;
//                        $row[$user->id][$step->slug]['Gas besparing'] = $savingsGas;
//                        $row[$user->id][$step->slug]['Gas besparing'] = $savingsGas;
//                    }
//
//                }
//            }
//            dd($row);
//        }
    }


    protected static function createOptions(Collection $collection, $value = 'name', $id = 'id', $nullPlaceholder = true)
    {
        $options = [];
        if ($nullPlaceholder) {
            $options[''] = '-';
        }
        foreach ($collection as $item) {
            $options[$item->$id] = $item->$value;
        }

        return $options;
    }
    public static function getContentStructure()
    {

        // General data - Elements (that are not queried later on step basis)
        $livingRoomsWindows = Element::where('short', 'living-rooms-windows')->first();
        $sleepingRoomsWindows = Element::where('short', 'sleeping-rooms-windows')->first();
        // General data - Services (that are not queried later on step basis)
        $heatpumpHybrid = Service::where('short', 'hybrid-heat-pump')->first();
        $heatpumpFull = Service::where('short', 'full-heat-pump')->first();
        $ventilation = Service::where('short', 'house-ventilation')->first();

        // Wall insulation
        $wallInsulation = Element::where('short', 'wall-insulation')->first();
        $facadeDamages = FacadeDamagedPaintwork::orderBy('order')->get();
        $surfaces = FacadeSurface::orderBy('order')->get();
        $facadePlasteredSurfaces = FacadePlasteredSurface::orderBy('order')->get();
        $energyLabels = EnergyLabel::all();

        // Insulated glazing
        $insulatedGlazings = InsulatingGlazing::all();
        $heatings = BuildingHeating::where('calculate_value', '<', 5)->get(); // we don't want n.v.t.
        $crackSealing = Element::where('short', 'crack-sealing')->first();
        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();
        $paintworkStatuses = PaintworkStatus::orderBy('order')->get();
        $woodRotStatuses = WoodRotStatus::orderBy('order')->get();

        // Floor insulation
        /** @var Element $floorInsulation */
        $floorInsulation = Element::where('short', 'floor-insulation')->first();
        $crawlspace = Element::where('short', 'crawlspace')->first();

        // Roof insulation
        $roofInsulation = Element::where('short', 'roof-insulation')->first();
        $roofTypes = RoofType::all();
        $roofTileStatuses = RoofTileStatus::orderBy('order')->get();
        // Same as RoofInsulationController->getMeasureApplicationsAdviceMap()
        $roofInsulationMeasureApplications = [
            'flat' => [
                Temperature::ROOF_INSULATION_FLAT_ON_CURRENT => MeasureApplication::where('short', 'roof-insulation-flat-current')->first(),
                Temperature::ROOF_INSULATION_FLAT_REPLACE => MeasureApplication::where('short', 'roof-insulation-flat-replace-current')->first(),
            ],
            'pitched' => [
                Temperature::ROOF_INSULATION_PITCHED_INSIDE => MeasureApplication::where('short', 'roof-insulation-pitched-inside')->first(),
                Temperature::ROOF_INSULATION_PITCHED_REPLACE_TILES => MeasureApplication::where('short', 'roof-insulation-pitched-replace-tiles')->first(),
            ],
        ];

        // High efficiency boiler
        // NOTE: building element hr-boiler tells us if it's there
        $hrBoiler = Service::where('short', 'hr-boiler')->first();
        $boiler = Service::where('short', 'boiler')->first();

        // Solar panels
        $solarPanels = Service::where('short', 'total-sun-panels')->first();
        $solarPanelsOptionsPeakPower = ['' => '-'] + SolarPanelsKeyFigures::getPeakPowers();
        $solarPanelsOptionsAngle = ['' => '-'] + SolarPanelsKeyFigures::getAngles();

        $heater = Service::where('short', 'sun-boiler')->first();
        $heaterOptionsAngle = ['' => '-'] + HeaterKeyFigures::getAngles();

        // Common
        $interests = Interest::orderBy('order')->get();
        $interestOptions = static::createOptions($interests);

        $structure = [
            'general-data' => [
                'building_features.surface' => [
                    'label' => Translation::translate('general-data.building-type.what-user-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.building_layers' => [
                    'label' => Translation::translate('general-data.building-type.how-much-building-layers.title'),
                    'type' => 'text',
                ],
                'building_features.roof_type_id' => [
                    'label' => Translation::translate('general-data.building-type.type-roof.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTypes),
                ],
                'building_features.energy_label_id' => [
                    'label' => Translation::translate('general-data.building-type.current-energy-label.title'),
                    'type' => 'select',
                    'options' => static::createOptions($energyLabels),
                ],
                'building_features.monument' => [
                    'label' => Translation::translate('general-data.building-type.is-monument.title'),
                    'type' => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                        0 => __('woningdossier.cooperation.radiobutton.unknown'),
                    ],
                ],
                // elements and services
                'element.'.$livingRoomsWindows->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $livingRoomsWindows->name,
                    'type' => 'select',
                    'options' => self::createOptions($livingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'element.'.$sleepingRoomsWindows->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $sleepingRoomsWindows->name,
                    'type' => 'select',
                    'options' => self::createOptions($sleepingRoomsWindows->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.element.'.$wallInsulation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $wallInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.element.'.$floorInsulation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $floorInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.element.'.$roofInsulation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $roofInsulation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],

                // services
                'service.'.$heatpumpHybrid->id => [
                    'label' => $heatpumpHybrid->name,
                    'type' => 'select',
                    'options' => static::createOptions($heatpumpHybrid->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$heatpumpHybrid->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $heatpumpHybrid->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$heatpumpFull->id => [
                    'label' => $heatpumpFull->name,
                    'type' => 'select',
                    'options' => static::createOptions($heatpumpFull->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$heatpumpFull->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $heatpumpFull->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'user_interest.service.'.$heater->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $heater->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$hrBoiler->id => [
                    'label' => $hrBoiler->name,
                    'type' => 'select',
                    'options' => static::createOptions($hrBoiler->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$hrBoiler->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $hrBoiler->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$boiler->id.'.service_value_id' => [
                    'label' => Translation::translate('boiler.boiler-type.title'),
                    'type' => 'select',
                    'options' => static::createOptions($boiler->values()->orderBy('order')->get(), 'value'),
                ],
                'service.'.$solarPanels->id . '.extra.value' => [
                    'label' => $solarPanels->name,
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.pieces.title'),
                ],
                'user_interest.service.'.$solarPanels->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $solarPanels->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$solarPanels->id.'.extra.year' => [
                    'label' => Translation::translate('general-data.energy-saving-measures.solar-panels.if-yes.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],

                // ventilation
                'service.'.$ventilation->id.'.service_value_id' => [
                    'label' => $ventilation->name,
                    'type' => 'select',
                    'options' => static::createOptions($ventilation->values()->orderBy('order')->get(), 'value'),
                ],
                'user_interest.service.'.$ventilation->id => [
                    //'label' => Translation::translate('general.change-interested.title', ['item' => $livingRoomsWindows->name]),
                    'label' => $ventilation->name . ': ' . Translation::translate('general.interested-in-improvement.title'),
                    'type' => 'select',
                    'options' => $interestOptions,
                ],
                'service.'.$ventilation->id.'.extra.year' => [
                    'label' => Translation::translate('general-data.energy-saving-measures.house-ventilation.if-mechanic.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],


                // habits
                'user_energy_habits.cook_gas' => [
                    'label' => Translation::translate('general-data.data-about-usage.cooked-on-gas.title'),
                    'type' => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                    ],
                ],
                'user_energy_habits.amount_electricity' => [
                    'label' => Translation::translate('general-data.data-about-usage.electricity-consumption-past-year.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.cubic-meters.title'),
                ],
                'user_energy_habits.amount_gas' => [
                    'label' => Translation::translate('general-data.data-about-usage.gas-usage-past-year.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.cubic-meters.title'),
                ],
                // user interests
            ],
            'wall-insulation' => [
                'element.'.$wallInsulation->id => [
                    'label' => Translation::translate('wall-insulation.intro.filled-insulation.title'),
                    'type' => 'select',
                    'options' => static::createOptions($wallInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.wall_surface' => [
                    'label' => Translation::translate('wall-insulation.optional.facade-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.insulation_wall_surface' => [
                    'label' => Translation::translate('wall-insulation.optional.insulated-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.cavity_wall' => [
                    'label' => Translation::translate('wall-insulation.intro.has-cavity-wall.title'),
                    'type' => 'select',
                    'options' => [
                        0 => __('woningdossier.cooperation.radiobutton.unknown'),
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                    ],
                ],
                'building_features.facade_plastered_painted' => [
                    'label' => Translation::translate('wall-insulation.intro.is-facade-plastered-painted.title'),
                    'type' => 'select',
                    'options' => [
                        1 => __('woningdossier.cooperation.radiobutton.yes'),
                        2 => __('woningdossier.cooperation.radiobutton.no'),
                        3 => __('woningdossier.cooperation.radiobutton.mostly'),
                    ],
                ],
                'building_features.facade_plastered_surface_id' => [
                    'label' => Translation::translate('wall-insulation.intro.surface-paintwork.title'),
                    'type' => 'select',
                    'options' => static::createOptions($facadePlasteredSurfaces),
                ],
                'building_features.facade_damaged_paintwork_id' => [
                    'label' => Translation::translate('wall-insulation.intro.damage-paintwork.title'),
                    'type' => 'select',
                    'options' => static::createOptions($facadeDamages),
                ],
                'building_features.wall_joints' => [
                    'label' => Translation::translate('wall-insulation.optional.flushing.title'),
                    'type' => 'select',
                    'options' => static::createOptions($surfaces),
                ],
                'building_features.contaminated_wall_joints' => [
                    'label' => Translation::translate('wall-insulation.optional.is-facade-dirty.title'),
                    'type' => 'select',
                    'options' => static::createOptions($surfaces),
                ],
                'calculations' => [
                    'gas_savings' => Translation::translate('wall-insulation.costs.gas.title'),
                    'co2_savings' => Translation::translate('wall-insulation.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                    'repair_joint' => Translation::translate('wall-insulation.taking-into-account.repair-joint.title'),
                    'clean_brickwork' => Translation::translate('wall-insulation.taking-into-account.clean-brickwork.title'),
                    'impregnate_wall' => Translation::translate('wall-insulation.taking-into-account.impregnate-wall.title'),
                    'paint_wall' => Translation::translate('wall-insulation.taking-into-account.wall-painting.title'),
                ]
            ],

            'insulated-glazing' => [
                'element.'.$crackSealing->id => [
                    'label' => Translation::translate('insulated-glazing.moving-parts-quality.title'),
                    'type' => 'select',
                    'options' => static::createOptions($crackSealing->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.window_surface' => [
                    'label' => Translation::translate('insulated-glazing.windows-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$frames->id => [
                    'label' => Translation::translate('insulated-glazing.paint-work.which-frames.title'),
                    'type' => 'select',
                    'options' => static::createOptions($frames->values()->orderBy('order')->get(), 'value'),
                ],
                'building_paintwork_statuses.last_painted_year' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.last-paintjob.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],
                'building_paintwork_statuses.paintwork_status_id' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.paint-damage-visible.title'),
                    'type' => 'select',
                    'options' => static::createOptions($paintworkStatuses),
                ],
                'building_paintwork_statuses.wood_rot_status_id' => [
                    'label' => Translation::translate('insulated-glazing.paint-work.wood-rot-visible.title'),
                    'type' => 'select',
                    'options' => static::createOptions($woodRotStatuses),
                ],
                'element.'.$woodElements->id => [
                    'label' => Translation::translate('insulated-glazing.paint-work.other-wood-elements.title'),
                    'type' => 'multiselect',
                    'options' => static::createOptions($woodElements->values()->orderBy('order')->get(), 'value'),
                ],
                'calculations' => [
                    'gas_savings' => Translation::translate('insulated-glazing.costs.gas.title'),
                    'co2_savings' => Translation::translate('insulated-glazing.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                    'paintwork' => Translation::translate('insulated-glazing.taking-into-account.paintwork.title'),
                    'paint_work' => Translation::translate('insulated-glazing.taking-into-account.paintwork_year.title')
                ],
            ],
            'floor-insulation' => [
                'element.'.$floorInsulation->id => [
                    'label' => Translation::translate('floor-insulation.floor-insulation.title'),
                    'type' => 'select',
                    'options' => static::createOptions($floorInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                'building_features.floor_surface' => [
                    'label' => Translation::translate('floor-insulation.surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'building_features.insulation_surface' => [
                    'label' => Translation::translate('floor-insulation.insulation-surface.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ],
                'element.'.$crawlspace->id.'.extra.has_crawlspace' => [
                    'label' => Translation::translate('floor-insulation.has-crawlspace.title'),
                    'type' => 'select',
                    'options' => __('woningdossier.cooperation.option'),
                ],
                'element.'.$crawlspace->id.'.extra.access' => [
                    'label' => Translation::translate('floor-insulation.crawlspace-access.title'),
                    'type' => 'select',
                    'options' => __('woningdossier.cooperation.option'),
                ],
                'element.'.$crawlspace->id.'.element_value_id' => [
                    'label' => Translation::translate('floor-insulation.crawlspace-height.title'),
                    'type' => 'select',
                    'options' => static::createOptions($crawlspace->values()->orderBy('order')->get(), 'value'),
                ],
                'calculations' => [
                    'gas_savings' => Translation::translate('floor-insulation.costs.gas.title'),
                    'co2_savings' => Translation::translate('floor-insulation.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),
                ]
            ],
            'roof-insulation' => [
                'element.'.$roofInsulation->id => [
                    'label' => $roofInsulation->name,
                    'type' => 'select',
                    'options' => static::createOptions($roofInsulation->values()->orderBy('order')->get(), 'value'),
                ],
                /*'building_roof_types.roof_type_id' => [
                    'label' => Translation::translate('roof-insulation.current-situation.roof-types.title'),
                    'type' => 'multiselect',
                    'options' => static::createOptions($roofTypes),
                ],*/
                'building_features.roof_type_id' => [
                    'label' => Translation::translate('roof-insulation.current-situation.main-roof.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTypes),
                ],

                'calculations' => [
                    'flat' => [
                        'gas_savings' => Translation::translate('roof-insulation.flat.costs.gas.title'),
                        'co2_savings' => Translation::translate('roof-insulation.flat.costs.co2.title'),
                        'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                        'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                        'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                        'indicative_costs_replacement' => Translation::translate('roof-insulation.flat.indicative-costs-replacement.title'),
                        'indicative_replacement_year' => Translation::translate('roof-insulation.flat.indicative-replacement.year.title')
                    ],
                    'pitched' => [
                        'gas_savings' => Translation::translate('roof-insulation.pitched.costs.gas.title'),
                        'co2_savings' => Translation::translate('roof-insulation.pitched.costs.co2.title'),
                        'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                        'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                        'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                        'indicative_costs_replacement' => Translation::translate('roof-insulation.pitched.indicative-costs-replacement.title'),
                        'indicative_replacement_year' => Translation::translate('roof-insulation.pitched.indicative-replacement.year.title')
                    ]
                ]
                // rest will be added later on
            ],
            'high-efficiency-boiler' => [
                'service.'.$boiler->id.'.extra.year' => [
                    'label' => Translation::translate('boiler.boiler-placed-date.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.year.title'),
                ],
                'calculations' => [
                    'gas_savings' => Translation::translate('high-efficiency-boiler.costs.gas.title'),
                    'co2_savings' => Translation::translate('high-efficiency-boiler.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),

                    'indicative_replacement_year' => Translation::translate('high-efficiency-boiler.indication-for-costs.indicative-replacement.title')
                ]
            ],
//		    'heat-pump' => [
//
//		    ],
            'solar-panels' => [
                'building_pv_panels.peak_power' => [
                    'label' => Translation::translate('solar-panels.peak-power.title'),
                    'type' => 'select',
                    'options' => $solarPanelsOptionsPeakPower,
                ],
                'building_pv_panels.number' => [
                    'label' => Translation::translate('solar-panels.number.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.pieces.title'),
                ],
                'building_pv_panels.pv_panel_orientation_id' => [
                    'label' => Translation::translate('solar-panels.pv-panel-orientation-id.title'),
                    'type' => 'select',
                    'options' => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
                ],
                'building_pv_panels.angle' => [
                    'label' => Translation::translate('solar-panels.angle.title'),
                    'type' => 'select',
                    'options' => $solarPanelsOptionsAngle,
                ],
                'calculations' => [
                    'yield_electricity' => Translation::translate('solar-panels.indication-for-costs.yield-electricity.title'),
                    'raise_own_consumption' => Translation::translate('solar-panels.indication-for-costs.raise-own-consumption.title'),

                    'gas_savings' => Translation::translate('solar-panels.costs.gas.title'),
                    'co2_savings' => Translation::translate('solar-panels.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),
                ],
            ],
            'heater' => [
                'service.'.$heater->id => [
                    'label' => $heater->name,
                    'type' => 'select',
                    'options' => static::createOptions($heater->values()->orderBy('order')->get(), 'value'),
                ],
                'building_heaters.pv_panel_orientation_id' => [
                    'label' => Translation::translate('heater.pv-panel-orientation-id.title'),
                    'type' => 'select',
                    'options' => static::createOptions(PvPanelOrientation::orderBy('order')->get()),
                ],
                'building_heaters.angle' => [
                    'label' => Translation::translate('heater.angle.title'),
                    'type' => 'select',
                    'options' => $heaterOptionsAngle,
                ],
                'calculations' => [
                    'consumption_water' => Translation::translate('heater.consumption-water.title'),
                    'consumption_gas' => Translation::translate('heater.consumption-gas.title'),

                    'size_boiler' => Translation::translate('heater.size-boiler.title'),
                    'size_collector' => Translation::translate('heater.size-collector.title'),

                    'production_heat' => Translation::translate('heater.indication-for-costs.production-heat'),
                    'percentage_consumption' => Translation::translate('heater.indication-for-costs.percentage-consumption.title'),

                    'gas_savings' => Translation::translate('heater.costs.gas.title'),
                    'co2_savings' => Translation::translate('heater.costs.co2.title'),
                    'savings_in_euro' => Translation::translate('general.costs.savings-in-euro.title'),
                    'indicative_costs' => Translation::translate('general.costs.indicative-costs.title'),
                    'comparable_rent' => Translation::translate('general.costs.comparable-rent.title'),
                ]
            ],
        ];

        /*
        // From GeneralDataController
        $interestElements = Element::whereIn('short', [
            'living-rooms-windows', 'sleeping-rooms-windows',
        ])->orderBy('order')->get();

        foreach ($interestElements as $interestElement) {
            $k = 'user_interest.element.'.$interestElement->id;
            $structure['general-data'][$k] = [
                'label' => 'Interest in '.$interestElement->name,
                'type' => 'select',
                'options' => $interestOptions,
            ];
        }
        */


        // Insulated glazing
        $igShorts = [
            'glass-in-lead', 'hrpp-glass-only',
            'hrpp-glass-frames', 'hr3p-frames',
        ];

        foreach ($igShorts as $igShort) {
            $measureApplication = MeasureApplication::where('short', $igShort)->first();
            if ($measureApplication instanceof MeasureApplication) {
                $structure['insulated-glazing']['user_interests.'.$measureApplication->id] = [
                    //'label' => 'Interest in '.$measureApplication->measure_name,
                    'label' => Translation::translate('general.change-interested.title', ['item' => $measureApplication->measure_name]),
                    'type' => 'select',
                    'options' => $interestOptions,
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.insulated_glazing_id'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.current-glass.title'),
                    'type' => 'select',
                    'options' => static::createOptions($insulatedGlazings),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.building_heating_id'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.rooms-heated.title'),
                    'type' => 'select',
                    'options' => static::createOptions($heatings),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.m2'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.m2.title'),
                    'type' => 'text',
                    'unit' => Translation::translate('general.unit.square-meters.title'),
                ];
                $structure['insulated-glazing']['building_insulated_glazings.'.$measureApplication->id.'.windows'] = [
                    'label' => $measureApplication->measure_name.': '.Translation::translate('insulated-glazing.'.$measureApplication->short.'.window-replace.title'),
                    'type' => 'text',
                ];
            }
        }

        // Roof insulation
        // have to refactor this
        // pitched = 1
        // flat = 2
        $pitched = new \stdClass();
        $pitched->id = 1;
        $pitched->short = 'pitched';
        $flat = new \stdClass();
        $flat->id = 2;
        $flat->short = 'flat';
        $roofTypes1 = collect([$pitched, $flat]);

        // $roofTypes1 should become $roofTypes->where('short', '!=', 'none');

        foreach ($roofTypes1 as $roofType) {
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.element_value_id'] = [
                'label' => Translation::translate('roof-insulation.current-situation.is-'.$roofType->short.'-roof-insulated.title'),
                'type' => 'select',
                'options' => static::createOptions($roofInsulation->values, 'value'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.roof_surface'] = [
                'label' => Translation::translate('roof-insulation.current-situation.'.$roofType->short.'-roof-surface.title'),
                'type' => 'text',
                'unit' => Translation::translate('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.insulation_roof_surface'] = [
                'label' => Translation::translate('roof-insulation.current-situation.insulation-'.$roofType->short.'-roof-surface.title'),
                'type' => 'text',
                'unit' => Translation::translate('general.unit.square-meters.title'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.zinc_replaced_date'] = [
                'label' => Translation::translate('roof-insulation.current-situation.zinc-replaced.title'),
                'type' => 'text',
                'unit' => Translation::translate('general.unit.year.title'),
            ];
            if ('flat' == $roofType->short) {
                $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.bitumen_replaced_date'] = [
                    'label' => Translation::translate('roof-insulation.current-situation.bitumen-insulated.title'),
                    'type'  => 'text',
                    'unit'  => Translation::translate('general.unit.year.title'),
                ];
            }
            if ('pitched' == $roofType->short) {
                $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.tiles_condition'] = [
                    'label' => Translation::translate('roof-insulation.current-situation.in-which-condition-tiles.title'),
                    'type' => 'select',
                    'options' => static::createOptions($roofTileStatuses),
                ];
            }
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.extra.measure_application_id'] = [
                'label' => Translation::translate('roof-insulation.'.$roofType->short.'-roof.insulate-roof.title'),
                'type' => 'select',
                'options' => static::createOptions(collect($roofInsulationMeasureApplications[$roofType->short]), 'measure_name'),
            ];
            $structure['roof-insulation']['building_roof_types.'.$roofType->id.'.building_heating_id'] = [
                'label' => Translation::translate('roof-insulation.'.$roofType->short.'-roof.situation.title'),
                'type' => 'select',
                'options' => static::createOptions($heatings),
            ];
        }

        return $structure;
    }

    /**
     * Write a csv file
     *
     * @param $headers
     * @param $contents
     *
     * @return \Closure
     */
    private static function write($headers, $contents)
    {

        // write the CSV file
        $callback = function () use ($headers, $contents) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers, ';');

            foreach ($contents as $contentRow) {
                fputcsv($file, $contentRow, ';');
            }

            fclose($file);
        };

        return $callback;
    }

    /**
     * Export a CSV file
     *
     * @param $callback
     * @param $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private static function export($callback, $filename)
    {
        $filename = str_replace('.csv', '', $filename);

        $browserHeaders = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename.'.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return \Response::stream($callback, 200, $browserHeaders);
    }
}