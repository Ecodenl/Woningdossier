<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\MeasureApplication;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionsAnswer;
use App\Models\Translation;
use App\Services\CsvExportService;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('cooperation.admin.cooperation.coordinator.reports.index');
    }

    public function downloadQuestionnaireResults()
    {

        $questionnaires = Questionnaire::all();
        $rows = [];
        $headers = [];

        foreach ($questionnaires as $questionnaire) {
            // set the question translation as header for the csv
            foreach ($questionnaire->questions as $question) {
                $headers[$question->id] = $question->name;
            }

            // foreach building get the
            foreach (Building::all() as $building) {
                $questionAnswersForCurrentQuestionnaire = \DB::table('questionnaires')
                    ->where('questionnaires.id', $questionnaire->id)
                    ->join('questions', 'questionnaires.id', '=', 'questions.questionnaire_id')
                    ->leftJoin('translations', function ($leftJoin) {
                        $leftJoin->on('questions.name', '=', 'translations.key')
                            ->where('language', '=', app()->getLocale());
                    })
                    ->leftJoin('questions_answers', function ($leftJoin) use ($building) {
                        $leftJoin->on('questions.id', '=', 'questions_answers.question_id')
                            ->where('questions_answers.building_id', '=', $building->id);
                    })
                    ->select('questions_answers.answer', 'questions.id as question_id')
                    ->get();


                foreach ($questionAnswersForCurrentQuestionnaire as $questionAnswerForCurrentQuestionnaire) {
                    $rows[$building->id][$questionAnswerForCurrentQuestionnaire->question_id] = $questionAnswerForCurrentQuestionnaire->answer;
                }
            }

        }

        return CsvExportService::export($headers, $rows, 'questionnaire-results');
    }

    public function downloadByYear()
    {
        // get user data
        $user = \Auth::user();
        $cooperation = $user->cooperations()->first();

        // get the users from the cooperations
        $users = $cooperation->users;

        // set the csv headers
        $csvHeaders = [
            __('woningdossier.cooperation.admin.reports.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.reports.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.reports.csv-columns.email'),
            __('woningdossier.cooperation.admin.reports.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.reports.csv-columns.mobilenumber'),
            __('woningdossier.cooperation.admin.reports.csv-columns.street'),
            __('woningdossier.cooperation.admin.reports.csv-columns.house-number'),
            __('woningdossier.cooperation.admin.reports.csv-columns.city'),
            __('woningdossier.cooperation.admin.reports.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.reports.csv-columns.country-code'),
        ];

        // put the measures inside the header array
        $thisYear = Carbon::now()->year;
        for ($startYear = $thisYear; $startYear <= ($thisYear + 100); ++$startYear) {
            $csvHeaders[] = $startYear;
        }

        $allUserMeasures = [];
        // new array for the userdata
        $rows = [];

        foreach ($users as $key => $user) {
            $building = $user->buildings()->first();
            $street = $building->street;
            $number = $building->number;
            $city = $building->city;
            $postalCode = $building->postal_code;
            $countryCode = $building->country_code;

            $firstName = $user->first_name;
            $lastName = $user->last_name;
            $email = $user->email;
            $phoneNumber = $user->phone_number;
            $mobileNumber = $user->mobile;

            // set the personal userinfo
            $row[$key] = [$firstName, $lastName, $email, $phoneNumber, $mobileNumber, $street, $number, $city, $postalCode, $countryCode];

            // set all the years in range
            for ($startYear = $thisYear; $startYear <= ($thisYear + 100); ++$startYear) {
                $row[$key][$startYear] = '';
            }

            // get the user measures / advices
            foreach ($user->actionPlanAdvices as $actionPlanAdvice) {
                $plannedYear = null == $actionPlanAdvice->planned_year ? $actionPlanAdvice->year : $actionPlanAdvice->planned_year;
                $measureName = $actionPlanAdvice->measureApplication->measure_name;

                if (is_null($plannedYear)) {
                    $plannedYear = $actionPlanAdvice->getAdviceYear();
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

        return CsvExportService::export($csvHeaders, $rows, 'by-year');
    }

    public function downloadByMeasure()
    {
        // get user data
        $user = \Auth::user();
        $cooperation = $user->cooperations()->first();

        // get the users from the cooperations
        $users = $cooperation->users;

        // set the csv headers
        $csvHeaders = [
            __('woningdossier.cooperation.admin.reports.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.reports.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.reports.csv-columns.email'),
            __('woningdossier.cooperation.admin.reports.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.reports.csv-columns.mobilenumber'),
            __('woningdossier.cooperation.admin.reports.csv-columns.street'),
            __('woningdossier.cooperation.admin.reports.csv-columns.house-number'),
            __('woningdossier.cooperation.admin.reports.csv-columns.city'),
            __('woningdossier.cooperation.admin.reports.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.reports.csv-columns.country-code'),
        ];

        // get all the measures
        $measures = MeasureApplication::all();

        // put the measures inside the header array
        foreach ($measures as $measure) {
            $csvHeaders[] = $measure->measure_name;
        }

        // new array for the userdata
        $rows = [];

        foreach ($users as $key => $user) {
            $building = $user->buildings()->first();
            $street = $building->street;
            $number = $building->number;
            $city = $building->city;
            $postalCode = $building->postal_code;
            $countryCode = $building->country_code;

            $firstName = $user->first_name;
            $lastName = $user->last_name;
            $email = $user->email;
            $phoneNumber = $user->phone_number;
            $mobileNumber = $user->mobile;

            // set the personal userinfo
            $row[$key] = [$firstName, $lastName, $email, $phoneNumber, $mobileNumber, $street, $number, $city, $postalCode, $countryCode];

            // set alle the measures to the user
            foreach ($measures as $measure) {
                $row[$key][$measure->measure_name] = '';
            }

            // get the user measures / advices
            foreach ($user->actionPlanAdvices as $actionPlanAdvice) {
                $plannedYear = null == $actionPlanAdvice->planned_year ? $actionPlanAdvice->year : $actionPlanAdvice->planned_year;
                $measureName = $actionPlanAdvice->measureApplication->measure_name;

                if (is_null($plannedYear)) {
                    $plannedYear = $actionPlanAdvice->getAdviceYear();
                }

                // fill the measure with the planned year
                $row[$key][$measureName] = $plannedYear;
            }

            $rows = $row;
        }

        return CsvExportService::export($csvHeaders, $rows, 'by-measure');
    }
}
