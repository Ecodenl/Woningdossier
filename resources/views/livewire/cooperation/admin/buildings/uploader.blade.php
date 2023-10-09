{{--
    This is a Tailwind (frontend) classed design, so we just push the classes into the head (for now... until the admin
    gets a rework).
--}}
@push('css')
    <style>
        /* Tailwind */
        .flex {
            display: flex;
        }
        .flex-wrap {
            flex-wrap: wrap;
        }
        .justify-center {
            justify-content: center;
        }
        .items-center {
            align-items: center;
        }
        .w-full {
            width: 100%;
        }
        .object-cover {
            object-fit: cover;
        }
        .pb-5 {
            padding-bottom: 1.25rem;
        }
        .w-1\/3 {
            width: 33.33%;
        }
        @media(min-width: 768px) {
            .md\:w-1\/4 {
                width: 25%;
            }
            .md\:w-1\/2 {
                width: 50%;
            }
        }

        /* Custom frontend */
        .form-error-label {
            color: #a94442; /* Backend text-danger colour */
            width: 100%;
            margin-top: 1rem;
        }
    </style>
@endpush

@php
    // When the temporary upload from Livewire fails, an error is thrown for the upload input. When the input is
    // multiple, one gets thrown for EVERY uploaded file. The error bag is shared with the view but not with
    // the component for whatever reason. We need this to be able to set the correct error message.
    if($errors->any()) {
        $errorBag = $errors->getBag('default')->getMessages();
        foreach($errorBag as $key => $error) {
            if(Str::startsWith($key, 'document')) {
                $errorToReport = __('validation.custom.uploader.wrong-files');

                if (is_array($error)) {
                    $error = Arr::first($error);
                }

                // So we know the temporary upload char limit is 150; if the error is equal to the translation,
                // we should add this error to inform the user.
                if ($error === __('validation.custom-rules.max-filename-length', ['length' => 150])) {
                    $errorToReport .= ' ' . __('validation.custom-rules.max-filename-length', [
                        'attribute' => __('validation.attributes')['documents.*'],
                        'length' => 150,
                    ]);
                }

                // Because the error key already exists, we can't "add" an error. It would never show. Instead
                // we override the message bag with a new bag that has all the old errors with the document key
                // overridden. It seems the MessageBag has no native way of setting a new message, it's all done in
                // the constructor. We use the __set magic method on the ViewErrorBag to update the default bag.
                $errors->default = new \Illuminate\Support\MessageBag(
                    array_merge($errorBag, ['document' => $errorToReport])
                );
                break;
            }
        }
    }
@endphp


<div class="flex flex-wrap w-full flex pb-5" x-data>
    @can('create', [\App\Models\Media::class, $inputSource, $building, $tag])
        <div class="flex flex-wrap w-full">
            @component('cooperation.frontend.layouts.components.form-group', [
               'label' => __('cooperation/admin/buildings.show.building-image'),
               'class' => 'w-1/3',
               'withInputSource' => false,
               'id' => 'file-uploader',
               'inputName' => 'document'
            ])
                <input wire:model="document" wire:loading.attr="disabled"
                       class="form-input" id="uploader" type="file" autocomplete="off"
                       {{-- This is a Livewire event we can capture --}}
                       x-on:livewire-upload-finish="livewire.emit('uploadDone')">
            @endcomponent
        </div>
    @endcan

    @if($image instanceof \App\Models\Media)
        @can('view', [$image, $inputSource, $building, $tag])
            <div class="flex flex-wrap justify-center items-center w-full md:w-1/4" id="uploaded-image">
                @if(in_array($image->extension, MediaHelper::getImageMimes(true)))
                    {{-- Image --}}
                    <img src="{{ $image->getUrl() }}" class="object-cover w-full">
                @else
                    {{-- Document --}}
                    <i class="icon-xxxl icon-other"></i>
                @endif

                @can('delete', [$image, $inputSource, $building, $tag])
                    <button x-on:click="if (confirm('@lang('cooperation/frontend/tool.my-plan.uploader.form.delete.confirm')')) {$wire.call('deleteOldImage'); close(); document.getElementById('uploaded-image').fadeOut(250);}"
                            class="flex px-4 btn btn-outline-red items-center">
                        @lang('cooperation/frontend/tool.my-plan.uploader.form.delete.title')
                        <i class="ml-2 icon-md icon-trash-can-red"></i>
                    </button>
                @endcan
            </div>

            <hr class="w-full">
        @endcan
    @endif
</div>
