<?php

namespace App\Jobs;

use App\Exports\Cooperation\TotalExport;
use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Scopes\GetValueScope;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class GenerateMeasureReport
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cooperation;
    protected $anonymizeData;
    protected $fileType;

    /**
     * GenerateMeasureReport constructor.
     *
     * @param  Cooperation  $cooperation
     * @param  bool  $anonymizeData
     */
    public function __construct(Cooperation $cooperation, FileType $fileType, bool $anonymizeData = false)
    {
        $this->fileType = $fileType;
        $this->cooperation = $cooperation;
        $this->anonymizeData = $anonymizeData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fileName = substr(Str::uuid(), 0, 7).$this->fileType->name.'.csv';

        $fileStorage = FileStorage::create([
            'cooperation_id' => $this->cooperation->id,
            'file_type_id' => $this->fileType->id,
            'content_type' => 'text/csv',
            'filename' => $fileName,
        ]);


        // temporary session to get the right data for the dumb.
        $residentInputSource = InputSource::findByShort('resident');
        HoomdossierSession::setInputSource($residentInputSource);
        HoomdossierSession::setInputSourceValue($residentInputSource);
        // Get the current cooperation
        $cooperation = $this->cooperation;

        // get the users from the cooperations
        $users = $cooperation->users;

        // set the csv headers
        if ($this->anonymizeData) {
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
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.mobilenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];

        }


        // get all the measures
        $measures = MeasureApplication::all();

        // put the measures inside the header array
        foreach ($measures as $measure) {
            $csvHeaders[] = $measure->measure_name;
        }

        // new array for the userdata
        $rows = [];
        $rows[] = $csvHeaders;
        // since we only want the reports from the resident
        $residentInputSource = InputSource::findByShort('resident');

        foreach ($users as $key => $user) {
            $building = $user->buildings()->first();
            if ($building instanceof Building) {


                /** @var Collection $conversationRequestsForBuilding */
                $conversationRequestsForBuilding = PrivateMessage::conversationRequestByBuildingId($building->id)
                    ->where('to_cooperation_id', $this->cooperation->id)->get();

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
                if ($this->anonymizeData) {
                    // set the personal userinfo
                    $row[$building->id] = [
                        $createdAt, $buildingStatus, $postalCode, $city,
                        $buildingType, $buildYear, $exampleBuilding
                    ];
                } else {
                    $row[$building->id] = [
                        $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                        $firstName, $lastName, $email, $phoneNumber, $mobileNumber,
                        $street, $number, $postalCode, $city,
                        $buildingType, $buildYear, $exampleBuilding
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
            }
            $rows = $row;
        }

        dd($rows);
//        \Session::forget('hoomdossier_session');

        // export the csv file
        Excel::store(new TotalExport($rows), $fileName, 'downloads', \Maatwebsite\Excel\Excel::CSV);


        $availableUntil = $fileStorage->created_at->addDays($this->fileType->duration ?? 5);
        $fileStorage->available_until = $availableUntil;
        $fileStorage->is_being_processed = false;
        $fileStorage->save();
    }
}
