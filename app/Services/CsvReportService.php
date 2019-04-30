<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use Carbon\Carbon;

class CsvReportService {

    /**
     * Generate a report by year.
     *
     * @return array
     */
    public static function byYear(): array
    {
        // get user data
        $user = \Auth::user();
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
                $street = $building->street;
                $number = $building->number;
                $city = $building->city;
                $postalCode = $building->postal_code;
                $countryCode = $building->country_code;

                $firstName = $user->first_name;
                $lastName = $user->last_name;
                $email = $user->email;
                $phoneNumber = "'".$user->phone_number;
                $mobileNumber = $user->mobile;

                // set the personal userinfo
                $row[$key] = [$firstName, $lastName, $email, $phoneNumber, $mobileNumber, $street, $number, $city, $postalCode, $countryCode];

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


        return ['rows' => $rows, 'headers' => $rows];
    }
}