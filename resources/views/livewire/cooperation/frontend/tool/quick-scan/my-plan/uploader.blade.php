<div class="flex flex-wrap w-full flex pb-5" x-data>
    @can('create', \App\Models\Media::class)
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
            <div class="flex w-2/3 justify-end pt-4">
                <p>
                    @lang('cooperation/frontend/tool.my-plan.uploader.help')
                </p>
            </div>
        </div>

        <hr class="w-full">
    @endcan

    <div class="flex flex-wrap w-full pl-8">
        @foreach($files as $file)
            @can('view', $file)
                <div class="flex flex-wrap w-1/4 justify-center mb-4 pr-8" x-data="modal()" wire:key="{{$file->id}}">
                    <div class="space-y-6 pb-16 w-full">
                        <div>
                            <div class="flex items-center justify-center h-60 w-full overflow-hidden rounded-lg">
                                @if(in_array($file->extension, MediaHelper::getImageMimes(true)))
                                    {{-- Image --}}
                                    <img src="{{ $file->getUrl() }}" class="object-cover">
                                @else
                                    {{-- Document --}}
                                    <i class="icon-xxxl icon-other"></i>
                                @endif
                            </div>
                            <div class="mt-4 flex items-start justify-between">
                                <div class="w-full">
                                    <h2 class="text-md font-medium text-gray-900 max-w-16/20 break-all">
                                        {{ "{$file->filename}.{$file->extension}" }}
                                    </h2>
                                </div>
                                @can('update', $file)
                                    @if(data_get($fileData, "{$file->id}.share_with_cooperation"))
                                        <i class="icon-md icon-show"></i>
                                    @else
                                        <i class="icon-md icon-hide"></i>
                                    @endif
                                @endcan
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">
                                @lang('cooperation/frontend/tool.my-plan.uploader.info.title')
                            </h3>
                            <dl class="mt-2 divide-y divide-gray-200 border-t border-gray-200">
                                <div class="flex justify-between py-3 text-sm font-medium">
                                    <dt class="text-gray-500">
                                        @lang('cooperation/frontend/tool.my-plan.uploader.info.uploaded-by')
                                    </dt>
                                    <dd class="whitespace-nowrap text-gray-900">
                                        {{ $file->inputSource->name }}
                                    </dd>
                                </div>
                                <div class="flex justify-between py-3 text-sm font-medium">
                                    <dt class="text-gray-500">
                                        @lang('cooperation/frontend/tool.my-plan.uploader.info.created-at')
                                    </dt>
                                    <dd class="whitespace-nowrap text-gray-900">
                                        {{ $file->created_at->format('d-m-Y') }}
                                    </dd>
                                </div>
                                <div class="flex justify-between py-3 text-sm font-medium">
                                    <dt class="text-gray-500">
                                        @lang('cooperation/frontend/tool.my-plan.uploader.info.type')
                                    </dt>
                                    <dd class="whitespace-nowrap text-gray-900">
                                        @lang("models/media.tags.{$fileData[$file->id]['tag']}")
                                    </dd>
                                </div>
                                @can('update', $file)
                                    <div class="flex py-3">
                                        <button x-on:click="toggle()" class="btn btn-purple">
                                            @lang('cooperation/frontend/tool.my-plan.uploader.form.header')
                                        </button>
                                    </div>
                                @endcan
                            </dl>
                        </div>
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
                               'label' => __('cooperation/frontend/tool.my-plan.uploader.form.title.label'),
                            ])
                                <input class="form-input" wire:model.debounce.500ms="fileData.{{$file->id}}.title"
                                       id="edit-file-title-{{$file->id}}"
                                       placeholder="@lang('cooperation/frontend/tool.my-plan.uploader.form.title.label')"
                                >
                            @endcomponent
                            @component('cooperation.frontend.layouts.components.form-group', [
                               'inputName' => "fileData.{$file->id}.description",
                               'class' => 'w-full',
                               'id' => "edit-file-description-{$file->id}",
                               'withInputSource' => false,
                               'label' => __('cooperation/frontend/tool.my-plan.uploader.form.description.label'),
                            ])
                                <textarea class="form-input" wire:model.debounce.500ms="fileData.{{$file->id}}.description"
                                          id="edit-file-description-{{$file->id}}"
                                          placeholder="@lang('cooperation/frontend/tool.my-plan.uploader.form.description.label')"
                                ></textarea>
                            @endcomponent
                            @component('cooperation.frontend.layouts.components.form-group', [
                               'inputName' => "fileData.{$file->id}.share_with_cooperation",
                               'class' => 'w-full',
                               'id' => "edit-file-share-with-cooperation-{$file->id}",
                               'withInputSource' => false,
                               'label' => __('cooperation/frontend/tool.my-plan.uploader.form.share-with-cooperation.label'),
                            ])
                                @php $shareVal = data_get($fileData, "{$file->id}.share_with_cooperation") ? 'show' : 'hide'; @endphp

                                <div class="checkbox-wrapper">
                                    <input id="edit-file-share-with-cooperation-{{$file->id}}" type="checkbox"
                                           wire:model="fileData.{{$file->id}}.share_with_cooperation"
                                           value="1">
                                    <label for="edit-file-share-with-cooperation-{{$file->id}}">
                                        <span class="checkmark"></span>
                                        <span class="flex items-center">
                                            @lang("cooperation/frontend/tool.my-plan.uploader.form.share-with-cooperation.options.{$shareVal}")
                                            <i class="ml-1 {{ "icon-{$shareVal}" }}"></i>
                                        </span>
                                    </label>
                                </div>
                            @endcomponent
                            @component('cooperation.frontend.layouts.components.form-group', [
                               'inputName' => "fileData.{$file->id}.tag",
                               'class' => 'w-full',
                               'id' => "edit-file-tag-{$file->id}",
                               'withInputSource' => false,
                               'label' => __('cooperation/frontend/tool.my-plan.uploader.form.tag.label'),
                            ])
                                {{-- In the develop heat pump upgrade, alpine select becomes usable for livewire. No point in re-inventing the wheel --}}
                                {{-- TODO: use when available --}}
        {{--                            @component('cooperation.frontend.layouts.components.alpine-select')--}}
                                    <select id="edit-file-tag-{{$file->id}}" class="form-input"
                                            wire:model="fileData.{{$file->id}}.tag">
                                        @foreach(MediaHelper::getFillableTagsForClass(\App\Models\Building::class) as $tag)
                                            <option value="{{ $tag }}">
                                                @lang("models/media.tags.{$tag}")
                                            </option>
                                        @endforeach
                                    </select>
        {{--                            @endcomponent--}}
                            @endcomponent
                        </div>
                        <div class="w-full border border-gray fixed left-0"></div>
                        <div class="flex flex-wrap justify-between mt-10">
                            <a href="{{ $file->getUrl() }}" target="_blank"
                               class="flex px-4 btn btn-purple items-center">
                                @lang('cooperation/frontend/tool.my-plan.uploader.form.download.title')
                                <i class="ml-2 icon-sm icon-arrow-down"></i>
                            </a>
                            @can('delete', $file)
                                {{-- It is important to have the wire:click AFTER the x-on:click, otherwise the confirm doesn't prevent wire:click --}}
                                <button x-on:click="if (confirm('@lang('cooperation/frontend/tool.my-plan.uploader.form.delete.confirm')')) {close(); $el.closest('{{"[wire\\\\:key=\"{$file->id}\"]"}}').fadeOut(250);} else { $event.stopImmediatePropagation(); }"
                                        wire:click="delete({{$file->id}})"
                                        class="flex px-4 btn btn-outline-red items-center">
                                    @lang('cooperation/frontend/tool.my-plan.uploader.form.delete.title')
                                    <i class="ml-2 icon-md icon-trash-can-red"></i>
                                </button>
                            @endcan
                        </div>
                    @endcomponent
                </div>
            @endcan
        @endforeach
    </div>
</div>
