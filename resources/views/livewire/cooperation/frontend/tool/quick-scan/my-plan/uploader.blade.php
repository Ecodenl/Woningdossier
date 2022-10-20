<div class="flex flex-wrap w-full flex pb-5" style="margin-top: 0;" {{-- Use style to override space-y-20 --}} x-data>
    <div class="flex flex-wrap w-full">
        @component('cooperation.frontend.layouts.components.form-group', [
           'label' => __('cooperation/frontend/tool.my-plan.uploader.add'),
           'class' => 'w-1/3',
           'withInputSource' => false,
           'id' => 'file-uploader',
           'inputName' => 'documents'
        ])
            <input wire:model="documents" wire:loading.attr="disabled"
                   class="form-input" id="uploader" type="file" multiple autocomplete="off"
                   x-on:livewire-upload-finish="livewire.emit('uploadDone')">
        @endcomponent
    </div>

    <hr class="w-full">

    <div class="flex flex-wrap w-full justify-end space-x-3">
        @foreach($files as $file)
            <div class="flex w-1/3">
                <p>{{ $file->filename }}</p>
                @if(in_array($file->extension, MediaHelper::getFileMimes(true)))
                    {{-- Image --}}

                @else
                    {{-- Document --}}

                @endif
            </div>
        @endforeach
    </div>
</div>
