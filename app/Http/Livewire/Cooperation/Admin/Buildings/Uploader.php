<?php

namespace App\Http\Livewire\Cooperation\Admin\Buildings;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Media;
use App\Rules\MaxFilenameLength;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Plank\Mediable\Facades\MediaUploader;

class Uploader extends Component
{
    use WithFileUploads,
        AuthorizesRequests;

    public Building $building;
    public string $tag;
    public InputSource $currentInputSource;
    public ?Media $image;
    public $document;

    protected $listeners = [
        'uploadDone' => 'saveFile',
    ];

    public function mount(Building $building, string $tag)
    {
        $this->building = $building;
        $this->tag = $tag;
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        $this->image = $building->firstMedia($tag);
    }

    public function render()
    {
        return view('livewire.cooperation.admin.buildings.uploader');
    }

    // Called from $listeners
    public function saveFile()
    {
        $document = $this->document;

        $validator = Validator::make(
            ['document' => $document],
            [
                'document' => [
                    'file',
                    'mimes:' . MediaHelper::getMimesForTag($this->tag),
                    'max:' . MediaHelper::getMaxFileSize($this->tag),
                    new MaxFilenameLength(),
                ],
            ]
        );

        if ($validator->passes()) {
            // For now just true since this is currently only used for the building image.
            $shareWithCooperation = true; // $this->building->user->allow_access;

            $this->deleteOldImage();

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

            $this->building->syncMedia($media, $this->tag);
            $this->image = $media;
        } else {
            $this->addError('documents', __('validation.custom.uploader.wrong-files'));
        }

        // Delete file after processed
        $document->delete();
        $this->document = null;
    }

    public function deleteOldImage()
    {
        // File limit is 1, but if we don't delete it, the media model remains, and if we don't delete from the model,
        // it won't delete the file.
        optional($this->building->media()->wherePivot('tag', $this->tag)->first())->delete();
        $this->image = null;
    }
}
