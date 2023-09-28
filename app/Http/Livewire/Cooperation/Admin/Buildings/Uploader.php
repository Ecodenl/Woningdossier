<?php

namespace App\Http\Livewire\Cooperation\Admin\Buildings;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class Uploader extends Component
{
    use WithFileUploads,
        AuthorizesRequests;

    public Building $building;
    public InputSource $inputSource;
    public ?Media $buildingImage;

    protected $listeners = [
        'uploadDone' => 'saveFiles',
    ];

    public function mount(Building $building)
    {
        $this->building = $building;
        $this->inputSource = HoomdossierSession::getInputSource(true);

        $this->buildingImage = $building->firstMedia(MediaHelper::BUILDING_IMAGE);
    }

    public function render()
    {
        return view('livewire.cooperation.admin.buildings.uploader');
    }
}
