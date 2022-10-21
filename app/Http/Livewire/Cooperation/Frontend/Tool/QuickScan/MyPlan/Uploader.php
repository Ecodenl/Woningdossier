<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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

        // We want all media regardless of tag
        $this->files = $building->media;
        foreach ($this->files as $file) {
            $this->fileData[$file->id] = [
                'title' => data_get($file->custom_properties, 'title'),
                'description' => data_get($file->custom_properties, 'description'),
                'tag' => $file->pivot->tag,
            ];
        }
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.my-plan.uploader');
    }

    public function updated(string $field)
    {
        if (Str::endsWith($field, ['title', 'description', 'tag'])) {
            $this->updateMedia($field);
        }
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
                    ->beforeSave(function ($media) {
                        $media->custom_properties = [
                            'share_with_cooperation' => $this->building->user->allow_access,
                        ];
                        $media->input_source_id = HoomdossierSession::getInputSource();
                    })
                    ->upload();

                $this->building->attachMedia($media, MediaHelper::GENERIC_FILE);
                $this->files[] = $media;
                $this->fileData[$media->id] = [
                    'title' => null,
                    'description' => null,
                    'tag' => MediaHelper::GENERIC_FILE,
                ];
            } else {
                $this->addError('documents', __('validation.custom.uploader.wrong-files'));
            }

            // Delete file after processed
            $document->delete();
        }

        $this->documents = [];
    }

    public function updateMedia(string $field)
    {
        // We know the field is built as "fileData.{$id}.{$name}", so we can safely get the second value.
        $fileId = explode('.', $field)[1];

        $file = $this->files->where('id', $fileId)->first();
        $fileData = $this->fileData[$fileId];

        $tagUpdated = Str::endsWith($field, 'tag');

        // Keep queries to a more minimum amount by only doing the required tasks
        if ($tagUpdated) {
            $this->building->detachMedia($file);
            $this->building->attachMedia($file, $fileData['tag']);
        } else {
            // We don't want to override the JSON, so we only set the properties if they're set
            $customProperties = $file->custom_properties;
            if (! empty($fileData['title'])) {
                $customProperties['title'] = $fileData['title'];
            }
            if (! empty($fileData['description'])) {
                $customProperties['description'] = $fileData['description'];
            }

            $file->update($fileData);
        }
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
