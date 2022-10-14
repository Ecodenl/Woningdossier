<?php

namespace App\Http\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Helpers\HoomdossierSession;
use App\Jobs\GenerateExampleBuildingCsv;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Services\FileTypeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CsvExport extends Component
{
    use AuthorizesRequests;

    public $cooperation;
    public $fileType;
    public $fileStorage;
    public $inputSource;

    public function mount(Cooperation $cooperation)
    {
        $this->cooperation = $cooperation;
        $this->fileType = FileType::findByShort('example-building-overview');
        $this->fileStorage = $this->fileType->files()->mostRecent()->first();
        $this->inputSource = HoomdossierSession::getInputSource(true);
    }

    public function render()
    {
        return view('livewire.cooperation.admin.example-buildings.csv-export');
    }

    public function generate()
    {
        $this->authorize('store', [$this->fileStorage, $this->fileType]);

        // and we create the new file
        $this->fileStorage = FileStorage::create([
            'cooperation_id' => $this->cooperation->id,
            'file_type_id' => $this->fileType->id,
            'filename' => (new FileTypeService($this->fileType))->niceFileName(),
            'input_source_id' => $this->inputSource->id,
            'is_being_processed' => true,
        ]);

        GenerateExampleBuildingCsv::dispatch($this->cooperation, $this->fileType, $this->fileStorage);
    }
}
