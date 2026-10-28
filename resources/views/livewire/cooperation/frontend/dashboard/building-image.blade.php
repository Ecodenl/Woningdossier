<div class="building-photo" x-data>
    @if($this->canViewImage())
        <img src="{{ route('cooperation.media.serve', ['cooperation' => $building->user->cooperation, 'media' => $image]) }}"
             alt="@lang('home.dashboard.building.photo.alt')">

        @if($this->canUpload())
            <label class="building-photo-replace" for="building-photo-input">
                @lang('home.dashboard.building.photo.replace')
            </label>
        @endif
    @elseif($this->canUpload())
        <label class="building-photo-empty" for="building-photo-input">
            <i class="icon-xxxl icon-house-dark"></i>
            <span>@lang('home.dashboard.building.photo.add')</span>
        </label>
    @else
        <div class="building-photo-empty">
            <i class="icon-xxxl icon-house-dark"></i>
        </div>
    @endif

    @if($this->canUpload())
        {{-- The input itself is off screen; the labels above are what the resident clicks. --}}
        <input wire:model.live="photo" wire:loading.attr="disabled"
               id="building-photo-input" class="sr-only" type="file" accept="image/*" autocomplete="off"
               x-on:livewire-upload-finish="$wire.dispatchSelf('uploadDone')">

        <p wire:loading wire:target="photo" class="building-photo-status">
            @lang('home.dashboard.building.photo.uploading')
        </p>

        @error('photo')
            <p class="building-photo-status text-red">{{ $message }}</p>
        @enderror
    @endif
</div>
