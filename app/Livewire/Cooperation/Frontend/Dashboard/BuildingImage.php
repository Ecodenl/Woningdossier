<?php

namespace App\Livewire\Cooperation\Frontend\Dashboard;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Media;
use App\Rules\MaxFilenameLength;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Plank\Mediable\Facades\MediaUploader;

/**
 * The photo of the house on the dashboard, and the means to put one there. The admin has had this
 * for a while; what is new is that the resident can do it for their own house, which is why the
 * policy is consulted here rather than assumed the way the admin component can.
 */
class BuildingImage extends Component
{
    use WithFileUploads;

    public Building $building;
    public InputSource $currentInputSource;
    public ?Media $image = null;
    public $photo;

    protected $listeners = [
        'uploadDone' => 'savePhoto',
    ];

    public function mount(Building $building): void
    {
        $this->building = $building;
        $this->currentInputSource = HoomdossierSession::getInputSource(true);
        $this->refreshImage();
    }

    public function render(): View
    {
        return view('livewire.cooperation.frontend.dashboard.building-image');
    }

    public function canUpload(): bool
    {
        return Gate::allows('create', [Media::class, $this->currentInputSource, $this->building, MediaHelper::BUILDING_IMAGE]);
    }

    public function canViewImage(): bool
    {
        return $this->image instanceof Media
            && Gate::allows('view', [$this->image, $this->currentInputSource]);
    }

    // Called from $listeners
    public function savePhoto(): void
    {
        if (! $this->canUpload()) {
            $this->discardUpload();

            return;
        }

        $maxSize = MediaHelper::getMaxFileSize(MediaHelper::BUILDING_IMAGE);

        $validator = Validator::make(
            ['photo' => $this->photo],
            [
                'photo' => [
                    'file',
                    'mimes:' . MediaHelper::getMimesForTag(MediaHelper::BUILDING_IMAGE),
                    'max:' . $maxSize,
                    new MaxFilenameLength(),
                ],
            ]
        );

        if ($validator->fails()) {
            $this->addError(
                'photo',
                __('validation.custom.uploader.wrong-files') . ' '
                . __('validation.custom.uploader.max-size', ['size' => number_format($maxSize / 1000, 0)])
            );
            $this->discardUpload();

            return;
        }

        $this->replaceExisting();

        $media = MediaUploader::fromSource($this->photo->getRealPath())
            ->toDestination('uploads', "buildings/{$this->building->id}")
            ->useFilename(pathinfo($this->photo->getClientOriginalName(), PATHINFO_FILENAME))
            ->beforeSave(function (Media $media) {
                $media->input_source_id = HoomdossierSession::getInputSource();
            })
            ->upload();

        $this->building->syncMedia($media, MediaHelper::BUILDING_IMAGE);
        $this->refreshImage();

        $this->discardUpload();
    }

    /**
     * A building holds one photo. The media row has to go along with the file, or the file stays
     * behind on disk with nothing pointing at it.
     */
    private function replaceExisting(): void
    {
        optional($this->building->media()->wherePivot('tag', MediaHelper::BUILDING_IMAGE)->first())->delete();

        $this->image = null;
    }

    private function refreshImage(): void
    {
        /** @phpstan-ignore assign.propertyType */
        $this->image = $this->building->firstMedia(MediaHelper::BUILDING_IMAGE);
    }

    private function discardUpload(): void
    {
        optional($this->photo)->delete();
        $this->photo = null;
    }
}
