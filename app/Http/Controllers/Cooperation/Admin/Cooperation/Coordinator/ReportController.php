<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\HoomdossierSession;
use App\Models\MeasureApplication;
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
//        // we only want the buildings from the current cooperation
//        $buildingsFromCurrentCooperation = \DB::table('cooperations')
//            ->where('cooperations.id', HoomdossierSession::getBuilding())
//            ->join('cooperation_user', 'cooperations.id', '=', 'cooperation_user.cooperation_id')
//            ->join('users', 'cooperation_user.user_id', '=', 'users.id')
//            ->leftJoin('buildings', 'users.id', '=', 'buildings.user_id')
//            ->select('buildings.*')
//            ->get();

        // we wil put the question names as header
        $csvHeaders = [];
        $rows = [];

        // get the questions ordered on question id
//        $questionsIdOrdered = \DB::table('questionnaires')
//            ->leftJoin('questions', 'questionnaires.id', '=', 'questions.questionnaire_id')
//            ->orderBy('questions.id', 'asc')
//            ->select('questions.*')
//            ->get();

        // working on query to get better code.
        $q = \DB::table('questionnaires')
            ->orderBy('questionnaires.step_id')
            ->leftJoin('questions', 'questionnaires.id', '=', 'questions.questionnaire_id')
            ->leftJoin('translations', function ($leftJoin) {
                $leftJoin->on('questions.name', '=', 'translations.key')
                    ->where('language', '=', app()->getLocale());
            })
            ->leftJoin('questions_answers', function ($leftJoin) {
                $leftJoin->on('questions.id', '=', 'questions_answers.question_id');
            })
            ->select('translations.translation as question_name', 'questions_answers.answer as question_answer', 'questions_answers.building_id as building_id')
            ->get()->toArray();

        dd($q);
//
//        $questionnaires = Questionnaire::all();
//
//        // loop through them and set the csv header
//        foreach ($questionnaires as $questionnaire) {
//            foreach ($questionnaire->questions as $question) {
//                $csvHeaders[$question->id] = $question->name;
//            }
//        }
//
//
//        foreach ($buildingsFromCurrentCooperation as $building) {
//
//            // get the answers from the building / user
//            $questionAnswers = QuestionsAnswer::where('building_id', $building->id)->orderBy('question_id')->get();
//
//            foreach ($questionAnswers as $questionAnswer) {
//                // this means the question has options and is a dropdown, checkbox, radiobutton etc.
//                // so we need to get the translation from that option
//                $questionOptions = $questionAnswer->question->questionOptions;
//                if ($questionOptions->isNotEmpty()) {
//                    $selectedAnswer = $questionOptions->find($questionAnswer->answer)->name;
//                } else {
//                    $selectedAnswer = $questionAnswer->answer;
//                }
//
//                $rows[$building->id][$questionAnswer->question_id] = $selectedAnswer;
//            }
//        }
//
//        // order and set some keys
//        foreach ($csvHeaders as $questionIdFromCsvHeader => $csvHeader) {
//            foreach ($rows as $buildingId => $userAnswers) {
//                foreach ($userAnswers as $questionIdFromUserAnswerRow => $userAnswer) {
//                    // if the question id from the csv header does not exist in a building id row
//                    // then the user did not answer that question
//                    // so we add the row with a default text
//                    if (!array_key_exists($questionIdFromCsvHeader, $rows[$buildingId])) {
//                        $rows[$buildingId][$questionIdFromCsvHeader] = "Niet beantwoord door bewoner";
//                    }
//                }
////                ksort($rows[$buildingId]);
//            }
//        }
//        dd($csvHeaders, $rows);
        return CsvExportService::export($csvHeaders, $rows, 'answers-by-users-on-custom-questions');

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
