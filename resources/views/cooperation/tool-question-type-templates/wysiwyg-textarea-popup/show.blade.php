<div x-data="modal()" class="w-full flex">
    <div class="w-full" x-data="tiptapEditor($wire.entangle('filledInAnswers.{{$toolQuestion->short}}'))" x-on:click="toggle()" wire:ignore>
        @component('cooperation.layouts.components.tiptap')
            <textarea wire:model.change="filledInAnswers.{{$toolQuestion->short}}" id="clickable-{{$toolQuestion->short}}"
                      class="form-input" placeholder="{{$toolQuestion->placeholder}}"
                      x-ref="editor"
                      @if(($disabled ?? false))
                          disabled
                      @else
                          x-on:input-updated.window="$el.setAttribute('disabled', true);"
                          x-on:input-update-processed.window="$el.removeAttribute('disabled');"
                      @endif
            ></textarea>
        @endcomponent
    </div>

    @component('cooperation.frontend.layouts.components.modal', ['class' => 'w-full md:w-1/2'])
        <div class="w-full" x-data="tiptapEditor($wire.entangle('filledInAnswers.{{$toolQuestion->short}}'))" wire:ignore>
            @component('cooperation.layouts.components.tiptap')
                <textarea wire:model.change="filledInAnswers.{{$toolQuestion->short}}" id="{{$toolQuestion->short}}"
                          class="form-input" placeholder="{{$toolQuestion->placeholder}}"
                          x-ref="editor"
                          @if(($disabled ?? false))
                              disabled
                          @else
                              x-on:input-updated.window="$el.setAttribute('disabled', true);"
                              x-on:input-update-processed.window="$el.removeAttribute('disabled');"
                          @endif
                ></textarea>
            @endcomponent
        </div>

        @slot('header')
            {{ $toolQuestion->name }}
        @endslot
        <div class="flex justify-end space-x-2 mt-5">
            <button class="btn btn-orange" wire:click="resetToOriginalAnswer('{{$toolQuestion->short}}')"
                    x-on:click="close()">
                @lang('default.buttons.cancel')
            </button>
            <button class="btn btn-purple" wire:click="saveSpecificToolQuestion('{{$toolQuestion->short}}')"
                    x-on:click="close()">
                @lang('default.buttons.save')
            </button>
        </div>
    @endcomponent
</div>