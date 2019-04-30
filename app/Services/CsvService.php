<?php

namespace App\Services;

use App\Helpers\Arr;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Scopes\GetValueScope;
use Carbon\Carbon;

class CsvService
{
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
     * CSV Report that returns the years by measure with full address data
     *
     * @param  string  $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function byMeasure($filename = 'by-measure')
    {
        // Get the current cooperation
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

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

                // set alle the measures to the user
                foreach ($measures as $measure) {
                    $row[$key][$measure->measure_name] = '';
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
            __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
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
                $postalCode = $building->postal_code;

                // set the personal userinfo
                $row[$key] = [$postalCode];

                // set alle the measures to the user
                foreach ($measures as $measure) {
                    $row[$key][$measure->measure_name] = '';
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
    public static function questionnaireResults($filename = 'questionnaire-results')
    {
        $questionnaires = Questionnaire::all();
        $rows           = [];

        // we only want to query on the buildings that belong to the cooperation of the current user
        $currentCooperation = Cooperation::find(HoomdossierSession::getCooperation());

        // get the users from the current cooperation that have the resident role
        $usersFromCooperation = $currentCooperation->users()->role('resident')->with('buildings')->get();

        foreach ($questionnaires as $questionnaire) {
            foreach ($usersFromCooperation as $user) {
                $building = $user->buildings()->first();

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
                       ->select('questions_answers.answer', 'questions.id as question_id', 'translations.translation as question_name')
                       ->get();

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
                        $rows[$building->id][$questionAnswerForCurrentQuestionnaire->question_name] = $answer;
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

        $headers = [];
        if ( ! empty($rows)) {
            $headers = array_keys(array_first($rows));
        }

        $csv = static::write($headers, $rows);

        return static::export($csv, $filename);

    }

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