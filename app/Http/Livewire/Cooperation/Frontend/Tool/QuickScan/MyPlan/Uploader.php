<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\Media;
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
    public array $fileData = [];

    protected $listeners = [
        'uploadDone' => 'saveFiles',
    ];

    public function mount(Building $building)
    {
        $this->building = $building;

        $this->files = $building->getMedia(MediaHelper::FILE);
        foreach ($this->files as $file) {
            $this->fileData[$file->id] = [
                //'title' => $file->title,
                //'description' => $file->description,
                //'input_source' => $file->inputSource->short,
            ];
        }
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

            if ($validator->passes()) {
                $media = MediaUploader::fromSource($document->getRealPath())
                    ->toDestination('uploads', "buildings/{$this->building->id}")
                    ->useFilename(pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME))
                    ->upload();

                $this->building->attachMedia($media, [MediaHelper::FILE]);
                $this->files[] = $media;
            } else {
                $this->addError('documents', __('validation.custom.uploader.wrong-files'));
            }

            // Delete file after processed
            $document->delete();
        }

        $this->documents = [];
    }

    public function delete($fileId)
    {
        $file = $this->files->where('id', $fileId)->first();

        if ($file instanceof Media) {
            $file->delete();
            $this->files = $this->files->keyBy('id')->forget($fileId)->values();
            unset($this->fileData[$fileId]);
        }
    }
}
