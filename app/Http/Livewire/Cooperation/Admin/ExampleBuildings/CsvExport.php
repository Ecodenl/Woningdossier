<?php

namespace App\Http\Livewire\Cooperation\Admin\ExampleBuildings;

use App\Jobs\GenerateExampleBuildingCsv;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\User;
use App\Services\FileTypeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class CsvExport extends Component
{
    use AuthorizesRequests;
    public $cooperation;
    public $fileType;
    public $fileStorage;


    public function mount(Cooperation $cooperation)
    {
        $this->cooperation = $cooperation;
        $this->fileType = FileType::findByShort('example-building-overview');
        $this->fileStorage = $this->fileType->files()->mostRecent()->first();
    }

    public function render()
    {
        return view('livewire.cooperation.admin.example-buildings.csv-export');
    }

    public function generate()
    {
        // and we create the new file
        $this->fileStorage = new FileStorage([
            'cooperation_id' => $this->cooperation->id,
            'file_type_id' => $this->fileType->id,
            'filename' => (new FileTypeService($this->fileType))->niceFileName(),
        ]);

        $this->authorize('store', [$this->fileStorage, $this->fileType]);
        $this->fileStorage->save();
        GenerateExampleBuildingCsv::dispatch($this->cooperation, $this->fileType, $this->fileStorage);
    }
}
