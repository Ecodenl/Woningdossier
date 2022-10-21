<div class="flex flex-wrap w-full flex pb-5" x-data>
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
                                <div>
                                    <h2 class="text-md font-medium text-gray-900">
                                        {{ "{$file->filename}.{$file->extension}" }}
                                    </h2>
                                </div>
                                @can('update', $file)
                                    <button type="button" class="ml-4 flex h-8 w-8 items-center justify-center rounded-full bg-white text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <!-- Heroicon name: outline/heart -->
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                        </svg>
                                        <span class="sr-only">Favorite</span>
                                    </button>
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
                                <div class="flex py-3">
                                    <button x-on:click="toggle()" class="btn btn-purple">
                                        @lang('cooperation/frontend/tool.my-plan.uploader.form.header')
                                    </button>
                                </div>
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
                            ])
                                <label class="form-label w-full" for="edit-file-title-{{$file->id}}">
                                    @lang('cooperation/frontend/tool.my-plan.uploader.form.title.label')
                                </label>
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
                            ])
                                <label class="form-label w-full" for="edit-file-description-{{$file->id}}">
                                    @lang('cooperation/frontend/tool.my-plan.uploader.form.description.label')
                                </label>
                                <textarea class="form-input" wire:model.debounce.500ms="fileData.{{$file->id}}.description"
                                          id="edit-file-description-{{$file->id}}"
                                          placeholder="@lang('cooperation/frontend/tool.my-plan.uploader.form.description.label')"
                                ></textarea>
                            @endcomponent
                            @component('cooperation.frontend.layouts.components.form-group', [
                               'inputName' => "fileData.{$file->id}.tag",
                               'class' => 'w-full',
                               'id' => "edit-file-tag-{$file->id}",
                               'withInputSource' => false,
                            ])
                                <label class="form-label w-full" for="edit-file-tag-{{$file->id}}">
                                    @lang('cooperation/frontend/tool.my-plan.uploader.form.tag.label')
                                </label>
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
