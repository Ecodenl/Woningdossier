<?php

namespace App\Http\Controllers\Admin;

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

        // set the headers and stuff
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=by-year.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // get the data
        $user = \Auth::user();
        $cooperation = $user->cooperations()->first();

        $users = $cooperation->users;

        // todo: find a cleaner way if possible

        foreach ($users as $user) {

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


            // create the user info array
            $allUserInfo[$user->id]["user"] = [$firstName, $lastName, $email, $phoneNumber, $mobileNumber, $street, $number, $city, $postalCode, $countryCode];

            // get the advices for a specific user
            $advices = UserActionPlanAdvice::getCategorizedActionPlan($user);

            // add the year titles
            $yearTitles = [];
            for ($startYear = 2018; $startYear <= 2118; $startYear++) {
                $yearTitles[] = $startYear;
            }

            foreach($advices as $measureType => $stepAdvices) {
                foreach ($stepAdvices as $step => $advicesForStep) {
                    foreach ($advicesForStep as $advice) {
                        $measure = $advice->measureApplication->measure_name;
                        $plannedYear = $advice->planned_year == null ? $advice->year : $advice->planned_year;
                        $allUserInfo[$user->id][$plannedYear][] = $measure;
                    }
                }
            }

        }

        $nameTitles =[
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


        // merge the titles toghetter to 1 array
        $titles = array_merge($nameTitles, $yearTitles);

        // loop through all the user info per user
        foreach ($allUserInfo as $userInfo) {

            // get the information off one specific user
            foreach ($userInfo as $plannedYear => $user) {
                // if the array key is a user extract the user information
                // and put it inside the array with the email as key
                if ($plannedYear == "user") {
                    $userEmail = $user[2];
                    foreach ($user as $userPersonalInfo) {
                        $result[$userEmail][] = $userPersonalInfo;
                    }
                }
                // if its not a user then its a year that holds the measures
                // put them inside the array
                if ($plannedYear != "user") {
                    for ($startYear = 2018; $startYear <= 2118; $startYear++) {
                        // if the startyear is = to the planned year
                        // put the measures inside the year
                        // else add the year with an empty string
                        if ($startYear == $plannedYear) {
                            $result[$userEmail][$plannedYear] = implode(", ", $user);
                        } else {
                            $result[$userEmail][] = "";
                        }

                    }

                }
            }
        }


        // write the CSV file
        $callback = function () use ($result, $titles) {

            $file = fopen('php://output', 'w');
            
            fputcsv($file, $titles, ';');

            foreach ($result as $userMeasures) {

                    fputcsv($file, $userMeasures, ';');
            }

            fclose($file);
        };


        return \Response::stream($callback, 200, $headers);
    }

    public function downloadByMeasure()
    {

    }
}
