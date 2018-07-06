<?php

namespace App\Http\Controllers\Admin;

use App\Models\Measure;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CsvExportController extends Controller
{
    public function index()
    {
        return view('admin.csv-export.index');
    }

    public function downloadByYear()
    {
        // set the headers for the browser
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=by-year.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // get user data
        $user = \Auth::user();
        $cooperation = $user->cooperations()->first();

        // get the users from the cooperations
        $users = $cooperation->users;

        // set the csv headers
        $csvHeaders =[
            __('woningdossier.cooperation.admin.csv-export.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.email'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.mobilenumber'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.street'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.house-number'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.city'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.country-code'),
        ];

        // put the measures inside the header array
        for ($startYear = 2018; $startYear <= 2118; $startYear++) {
            $csvHeaders[] = $startYear;
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

            // set all the years in a range from 2018 to 2118
            for ($startYear = 2018; $startYear <= 2118; $startYear++) {
                $row[$key][$startYear] = "";
            }

            // get the user measures / advices
            foreach ($user->actionPlanAdvices as $actionPlanAdvice) {

                $plannedYear = $actionPlanAdvice->planned_year == null ? $actionPlanAdvice->year : $actionPlanAdvice->planned_year;
                $measureName = $actionPlanAdvice->measureApplication->measure_name;

                if (is_null($plannedYear)){
                    $plannedYear = $actionPlanAdvice->getAdviceYear();
                }

                // create a new array with the measures for the user connected to the planned year
                $allUserMeasures[$plannedYear][] = $measureName;

            }

            // loop through the user measures and add them to the row
            foreach ($allUserMeasures as $year => $userMeasures) {
                $row[$key][$year] = implode(", ", $userMeasures);
            }

            $rows = $row;

        }



        // write the CSV file
        $callback = function () use ($csvHeaders, $rows) {

            $file = fopen('php://output', 'w');

            fputcsv($file, $csvHeaders, ';');

            foreach ($rows as $userMeasures) {

                fputcsv($file, $userMeasures, ';');
            }

            fclose($file);
        };


        return \Response::stream($callback, 200, $headers);
    }

//    public function downloadByYear()
//    {
//
//        // set the headers and stuff
//        $headers = [
//            "Content-type" => "text/csv",
//            "Content-Disposition" => "attachment; filename=by-year.csv",
//            "Pragma" => "no-cache",
//            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
//            "Expires" => "0"
//        ];
//
//        // get the data
//        $user = \Auth::user();
//        $cooperation = $user->cooperations()->first();
//
//        $users = $cooperation->users;
//
//        // todo: find a cleaner way if possible
//
//        foreach ($users as $user) {
//
//            $building = $user->buildings()->first();
//            $street = $building->street;
//            $number = $building->number;
//            $city = $building->city;
//            $postalCode = $building->postal_code;
//            $countryCode = $building->country_code;
//
//            $firstName = $user->first_name;
//            $lastName = $user->last_name;
//            $email = $user->email;
//            $phoneNumber = $user->phone_number;
//            $mobileNumber = $user->mobile;
//
//
//            // create the user info array
//            $allUserInfo[$user->id]["user"] = [$firstName, $lastName, $email, $phoneNumber, $mobileNumber, $street, $number, $city, $postalCode, $countryCode];
//
//            // get the advices for a specific user
//            $advices = UserActionPlanAdvice::getCategorizedActionPlan($user);
//
//            // add the year titles
//            $yearTitles = [];
//            for ($startYear = 2018; $startYear <= 2118; $startYear++) {
//                $yearTitles[] = $startYear;
//            }
//
//            foreach($advices as $measureType => $stepAdvices) {
//                foreach ($stepAdvices as $step => $advicesForStep) {
//                    foreach ($advicesForStep as $advice) {
//                        $measure = $advice->measureApplication->measure_name;
//                        $plannedYear = $advice->planned_year == null ? $advice->year : $advice->planned_year;
//                        $allUserInfo[$user->id][$plannedYear][] = $measure;
//                    }
//                }
//            }
//
//        }
//
//        $nameTitles =[
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.first-name'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.last-name'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.email'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.phonenumber'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.mobilenumber'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.street'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.house-number'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.city'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.zip-code'),
//            __('woningdossier.cooperation.admin.csv-export.csv-columns.country-code'),
//        ];
//
//
//        // merge the titles toghetter to 1 array
//        $titles = array_merge($nameTitles, $yearTitles);
//
//        // loop through all the user info per user
//        foreach ($allUserInfo as $userInfo) {
//
//            // get the information off one specific user
//            foreach ($userInfo as $plannedYear => $user) {
//                // if the array key is a user extract the user information
//                // and put it inside the array with the email as key
//                if ($plannedYear == "user") {
//                    $userEmail = $user[2];
//                    foreach ($user as $userPersonalInfo) {
//                        $result[$userEmail][] = $userPersonalInfo;
//                    }
//                }
//                // if its not a user then its a year that holds the measures
//                // put them inside the array
//                if ($plannedYear != "user") {
//                    for ($startYear = 2018; $startYear <= 2118; $startYear++) {
//                        // if the startyear is = to the planned year
//                        // put the measures inside the year
//                        // else add the year with an empty string
//                        if ($startYear == $plannedYear) {
//                            $result[$userEmail][$plannedYear] = implode(", ", $user);
//                        } else {
//                            $result[$userEmail][] = "";
//                        }
//
//                    }
//
//                }
//            }
//        }
//
//
//        // write the CSV file
//        $callback = function () use ($result, $titles) {
//
//            $file = fopen('php://output', 'w');
//
//            fputcsv($file, $titles, ';');
//
//            foreach ($result as $userMeasures) {
//
//                    fputcsv($file, $userMeasures, ';');
//            }
//
//            fclose($file);
//        };
//
//
//        return \Response::stream($callback, 200, $headers);
//    }

    public function downloadByMeasure()
    {

        // set the headers for the browser
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=by-measure.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // get user data
        $user = \Auth::user();
        $cooperation = $user->cooperations()->first();

        // get the users from the cooperations
        $users = $cooperation->users;

        // set the csv headers
        $csvHeaders =[
            __('woningdossier.cooperation.admin.csv-export.csv-columns.first-name'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.last-name'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.email'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.phonenumber'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.mobilenumber'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.street'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.house-number'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.city'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.zip-code'),
            __('woningdossier.cooperation.admin.csv-export.csv-columns.country-code'),
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
                $row[$key][$measure->measure_name] = "";
            }

            // get the user measures / advices
            foreach ($user->actionPlanAdvices as $actionPlanAdvice) {

                $plannedYear = $actionPlanAdvice->planned_year == null ? $actionPlanAdvice->year : $actionPlanAdvice->planned_year;
                $measureName = $actionPlanAdvice->measureApplication->measure_name;

                if (is_null($plannedYear)){
                    $plannedYear = $actionPlanAdvice->getAdviceYear();
                }

                // fill the measure with the planned year
                $row[$key][$measureName] = $plannedYear;
            }

            $rows = $row;

        }


        // write the CSV file
        $callback = function () use ($csvHeaders, $rows) {

            $file = fopen('php://output', 'w');

            fputcsv($file, $csvHeaders, ';');

            foreach ($rows as $userMeasures) {

                fputcsv($file, $userMeasures, ';');
            }

            fclose($file);
        };


        return \Response::stream($callback, 200, $headers);
    }
}
