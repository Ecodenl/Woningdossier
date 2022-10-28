<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Plank\Mediable\Facades\MediaUploader;

class Uploader extends Component
{
    use WithFileUploads,
        AuthorizesRequests;

    public $building;
    public $inputSource;
    public $documents;
    public $files;
    public array $fileData = [];

    protected $listeners = [
        'uploadDone' => 'saveFiles',
    ];

    public function mount(Building $building)
    {
        $this->building = $building;
        $this->inputSource = HoomdossierSession::getInputSource(true);

        // We want all media regardless of tag
        $this->files = $building->media;
        foreach ($this->files as $file) {
            $this->fileData[$file->id] = [
                'title' => data_get($file->custom_properties, 'title'),
                'description' => data_get($file->custom_properties, 'description'),
                'share_with_cooperation' => data_get($file->custom_properties, 'share_with_cooperation'),
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
        if (Str::endsWith($field, ['title', 'description', 'share_with_cooperation', 'tag'])) {
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
                // If we're in an iframe currently, then we know it's being done from the admin and share should be true.
                // Otherwise, we check if the user has allowed cooperation access
                $shareWithCooperation = (bool) (request()->input('iframe', false) ?: $this->building->user->allow_access);

                $media = MediaUploader::fromSource($document->getRealPath())
                    ->toDestination('uploads', "buildings/{$this->building->id}")
                    ->useFilename(pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME))
                    ->beforeSave(function ($media) use ($shareWithCooperation) {
                        $media->custom_properties = [
                            'share_with_cooperation' => $shareWithCooperation,
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
                    'share_with_cooperation' => $shareWithCooperation,
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

        $this->authorize('update', [$file, $this->inputSource, $this->building]);

        $fileData = $this->fileData[$fileId];

        $tagUpdated = Str::endsWith($field, 'tag');

        // Keep queries to a more minimum amount by only doing the required tasks
        if ($tagUpdated) {
            $this->building->detachMedia($file);
            $this->building->attachMedia($file, $fileData['tag']);
        } else {
            $customProperties = $file->custom_properties ?? [];

            $customProperties['title'] = data_get($fileData, 'title', '');
            $customProperties['description'] = data_get($fileData, 'description', '');
            if (Auth::user()->can('shareWithCooperation', [$file, $this->inputSource, $this->building])) {
                // Ensure we don't edit this if we're not allowed to
                $customProperties['share_with_cooperation'] = (bool) data_get($fileData, 'share_with_cooperation');
            }

            $file->update([
                'custom_properties' => $customProperties,
            ]);
        }
    }

    public function delete($fileId)
    {
        $file = $this->files->where('id', $fileId)->first();

        $this->authorize('delete', [$file, $this->inputSource, $this->building]);

        if ($file instanceof Media) {
            $file->delete();
            $this->files = $this->files->keyBy('id')->forget($fileId)->values();
            unset($this->fileData[$fileId]);
        }
    }
}
