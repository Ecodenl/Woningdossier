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

    <div class="flex flex-wrap w-full pad-x-3">
        @foreach($files as $file)
            <div class="flex flex-wrap w-1/4 justify-center mb-4" x-data="modal()" wire:key="{{$file->id}}">
                <p class="mb-2 italic cursor-pointer hover:opacity-75" x-on:click="toggle()">
                    {{ "{$file->filename}.{$file->extension} - {$file->created_at->format('d-m-Y')}" }}
                </p>
                <div class="max-h-60 flex justify-center">

                    @if(in_array($file->extension, MediaHelper::getImageMimes(true)))
                        {{-- Image --}}
                            <img src="{{ $file->getUrl() }}" class="img-responsive max-h-full border border-blue-500 cursor-pointer hover:opacity-75"
                                 x-on:click="toggle()">
                    @else
                        {{-- Document --}}
                        <i class="icon-xxxl icon-other cursor-pointer hover:opacity-75" x-on:click="toggle()"></i>
                    @endif
                </div>

                @component('cooperation.frontend.layouts.components.modal', [
                    'header' => __('cooperation/frontend/tool.my-plan.uploader.form.header')
                ])
                    <div class="flex flex-wrap mb-5">
                        @component('cooperation.frontend.layouts.components.form-group', [
                           'inputName' => "fileData.{$file->id}.title",
                           'class' => 'w-full -mt-4',
                           'id' => "edit-file-title-{$file->id}",
                           'withInputSource' => false,
                       ])
                            <input class="form-input" wire:model="fileData.{{$file->id}}.title"
                                   id="edit-file-title-{{$file->id}}"
                                   placeholder="@lang('cooperation/frontend/tool.my-plan.uploader.form.title.placeholder')"
                            >
                        @endcomponent
                        @component('cooperation.frontend.layouts.components.form-group', [
                           'inputName' => "fileData.{$file->id}.description",
                           'class' => 'w-full',
                           'id' => "edit-file-description-{$file->id}",
                           'withInputSource' => false,
                       ])
                            <textarea class="form-input" wire:model="fileData.{{$file->id}}.description"
                                      id="edit-file-description-{{$file->id}}"
                                      placeholder="@lang('cooperation/frontend/tool.my-plan.uploader.form.description.placeholder')"
                        ></textarea>
                        @endcomponent
                    </div>
                    <div class="w-full border border-gray fixed left-0"></div>
                    <div class="flex flex-wrap justify-start space-x-2 mt-10 px-1">
                        <button class="rounded-full border-2 border-blue-500 bg-white hover:bg-blue hover:bg-opacity-25 transition duration-250 h-10 w-10 flex items-center justify-center">
                            <i class="icon-md icon-arrow-down"></i>
                        </button>
                        <button x-on:click="if (! confirm('@lang('cooperation/frontend/tool.my-plan.uploader.form.delete.confirm')')) { $event.stopImmediatePropagation(); }"
                                wire:click="delete({{$file->id}})"
                                class="rounded-full border-2 border-red bg-white hover:bg-red hover:bg-opacity-25 transition duration-250 h-10 w-10 flex items-center justify-center">
                            <i class="icon-md icon-trash-can-red"></i>
                        </button>
                    </div>
                @endcomponent
            </div>
        @endforeach
    </div>
</div>
