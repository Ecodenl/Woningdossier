<?php

namespace App\Jobs;

use App\Calculations\FloorInsulation;
use App\Calculations\Heater;
use App\Calculations\HighEfficiencyBoiler;
use App\Calculations\InsulatedGlazing;
use App\Calculations\RoofInsulation;
use App\Calculations\SolarPanel;
use App\Calculations\WallInsulation;
use App\Exports\Cooperation\CsvExport;
use App\Exports\Cooperation\TotalExport;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\Str;
use App\Helpers\ToolHelper;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
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
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Services\CsvService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class GenerateTotalReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cooperation;
    protected $anonymizeData;
    protected $fileType;

    /**
     * GenerateTotalReport constructor.
     *
     * @param  Cooperation  $cooperation
     * @param  bool  $anonymizeData
     */
    public function __construct(Cooperation $cooperation, FileType $fileType, $anonymizeData = false)
    {
        $this->fileType = $fileType;
        $this->anonymizeData = $anonymizeData;
        $this->cooperation = $cooperation;
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

        $rows = CsvService::totalReport($this->cooperation, $this->anonymizeData);

        \Session::forget('hoomdossier_session');

        // export the csv file
        Excel::store(new CsvExport($rows), $fileName, 'downloads', \Maatwebsite\Excel\Excel::CSV);


        $availableUntil = $fileStorage->created_at->addDays($this->fileType->duration ?? 5);
        $fileStorage->available_until = $availableUntil;
        $fileStorage->is_being_processed = false;
        $fileStorage->save();
    }

}
