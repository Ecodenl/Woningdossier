<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\MediaHelper;
use App\Models\Building;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Plank\Mediable\Facades\MediaUploader;

class Uploader extends Component
{
    use WithFileUploads;

    public $building;
    public $documents;
    public $files;

    protected $listeners = [
        'uploadDone' => 'saveFiles',
    ];

    public function mount(Building $building)
    {
        $this->building = $building;

        $this->files = $building->getMedia(MediaHelper::FILE);
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.my-plan.uploader');
    }

    public function saveFiles()
    {
        $this->resetErrorBag('documents');

        foreach ($this->documents as $index => $document) {
            $validator = Validator::make(
                ['document' => $document],
                [
                    'document' => [
                        'file',
                        'mimes:' . MediaHelper::getAllMimes(),
                        'max:' . MediaHelper::getMaxFileSize(),
                    ],
                ]
            );

            if ($validator->fails()) {
                // TODO: This isn't working...
                $this->addError('documents', __('validation.custom.uploader.wrong-files'));
            }

            $validator->validate();

            // TODO: Broken
            $media = MediaUploader::fromSource($document)
                ->toDestination('uploads', "buildings/{$this->building->id}")
                ->useFilename($document->getClientOriginalName())
                ->upload();

            $this->building->syncMedia($media, [MediaHelper::FILE]);
            $this->files[] = $media;
        }

        $this->documents = [];
    }
}
